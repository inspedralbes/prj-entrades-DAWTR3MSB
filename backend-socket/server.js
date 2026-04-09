const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const db = require('./db');

const app = express();
app.use(cors());
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST']
  }
});

// --- Rutes API (Mínimes per a Socket health check i comunicació interna) ---
app.get('/socket-status', (req, res) => {
  res.json({ status: 'Socket Server Online' });
});

// Endpoint per permetre que el backend Laravel (API) faci broadcast d'esdeveniments
app.post('/internal/broadcast', (req, res) => {
  const { event, data } = req.body;
  if (event && data) {
    io.emit(event, data);
    return res.json({ success: true });
  }
  res.status(400).json({ error: 'Missing event or data' });
});

// --- Lògica de Temps Real (Dia 6-7) ---
// Nota: Laravel porta l'API REST al port 8000. 
// Aquest servidor Node només gestiona els WebSockets al port 3001.

io.on('connection', (socket) => {
  console.log(`Usuari connectat: ${socket.id}`);

  // Event: Un usuari vol reservar o alliberar un seient
  socket.on('reservar_seient', async ({ seientId, esdevenimentId, estat }) => {
    try {
      const targetEstat = estat || 'reservat';

      if (targetEstat === 'disponible') {
        // L'usuari vol deseleccionar el seient
        await db.query('UPDATE seients SET estat = "disponible" WHERE id = ?', [seientId]);
        console.log(`Seient ${seientId} alliberat per ${socket.id}`);
        io.emit('seient_actualitzat', { seientId, estat: 'disponible' });
        return;
      }

      // 1. Verifiquem disponibilitat real a la BD per reservar
      const [[seient]] = await db.query('SELECT * FROM seients WHERE id = ? AND estat = "disponible"', [seientId]);

      if (seient) {
        // 2. Marquem com a reservat a la BD
        await db.query('UPDATE seients SET estat = "reservat" WHERE id = ?', [seientId]);
        
        console.log(`Seient ${seientId} reservat per ${socket.id}`);
        
        // 3. Notifiquem a TOTHOM
        io.emit('seient_actualitzat', { seientId, estat: 'reservat' });

        // 4. Temporitzador d'alliberament (Dia 7 avançat)
        setTimeout(async () => {
          const [[check]] = await db.query('SELECT estat FROM seients WHERE id = ?', [seientId]);
          if (check && check.estat === 'reservat') {
            await db.query('UPDATE seients SET estat = "disponible" WHERE id = ?', [seientId]);
            io.emit('seient_actualitzat', { seientId, estat: 'disponible' });
          }
        }, 180000); // 3 minuts
      } else {
        socket.emit('error_concurrencia', { message: 'Aquest seient ja no està disponible!' });
      }
    } catch (error) {
      console.error('Error en reserva/alliberament:', error);
    }
  });

  socket.on('disconnect', () => {
    console.log(`Usuari desconnectat: ${socket.id}`);
  });
});

const PORT = process.env.PORT || 3001;
server.listen(PORT, () => {
  console.log(`🚀 Servidor backend escoltant al port ${PORT}`);
});
