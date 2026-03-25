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

// --- Rutes API REST ---

// Llistat d'esdeveniments (Per a la Portada - SSR)
app.get('/api/esdeveniments', async (req, res) => {
  try {
    const [rows] = await db.query('SELECT * FROM esdeveniments');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: 'Error al carregar esdeveniments' });
  }
});

// Detall d'un esdeveniment i els seus seients
app.get('/api/esdeveniments/:id', async (req, res) => {
  try {
    const [[esdeveniment]] = await db.query('SELECT * FROM esdeveniments WHERE id = ?', [req.params.id]);
    if (!esdeveniment) return res.status(404).json({ error: 'Esdeveniment no trobat' });

    const [seients] = await db.query('SELECT * FROM seients WHERE esdeveniment_id = ?', [req.params.id]);
    res.json({ ...esdeveniment, seients });
  } catch (error) {
    res.status(500).json({ error: 'Error al carregar el detall de l esdeveniment' });
  }
});

// Ruta bàsica per comprovar que l'API funciona
app.get('/api/status', (req, res) => {
  res.json({ 
    status: 'Online', 
    project: 'Entradas huevostyle',
    author: 'Marcos suarez'
  });
});

// Connexió inicial de temps real amb Socket.IO
io.on('connection', (socket) => {
  console.log(`Un client s'ha connectat: ${socket.id}`);

  // Gestionar la desconnexió
  socket.on('disconnect', () => {
    console.log(`Client desconnectat: ${socket.id}`);
  });
});

const PORT = process.env.PORT || 3001;
server.listen(PORT, () => {
  console.log(`🚀 Servidor backend escoltant al port ${PORT}`);
});
