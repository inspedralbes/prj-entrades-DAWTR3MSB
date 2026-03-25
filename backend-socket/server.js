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

// --- Dades de record (Mock) per si la BD no està llista ---
const mockEsdeveniments = [
  { id: 1, nom: 'Concert TR3 Final (Mock)', data_hora: new Date(), recinte: 'Palau Sant Jordi', descripcio: 'Funcionant en mode prova.' }
];
let mockSeients = Array.from({ length: 50 }, (_, i) => ({
  id: i + 1, esdeveniment_id: 1, fila: Math.floor(i / 10) + 1, numero: i + 1, estat: 'lliure', preu: 45
}));

// --- Rutes API (Mínimes per a Socket health check) ---
app.get('/socket-status', (req, res) => {
  res.json({ status: 'Socket Server Online' });
});

// --- Lògica de Temps Real (Dia 6) ---
// Nota: Laravel porta l'API REST al port 8000. 
// Aquest servidor Node només gestiona els WebSockets al port 3001.

io.on('connection', (socket) => {
  console.log(`Usuari connectat: ${socket.id}`);

  // Event: Un usuari vol reservar un seient
  socket.on('reservar_seient', async ({ seientId, esdevenimentId }) => {
    try {
      // 1. Verifiquem disponibilitat real a la BD
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
      console.error('Error en reserva:', error);
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
