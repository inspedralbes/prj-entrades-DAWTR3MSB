const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: '*', // Es canviarà en producció per seguretat
    methods: ['GET', 'POST']
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
