import { io } from 'socket.io-client'

export default defineNuxtPlugin(() => {
  if (process.server) return

  const host = window.location.hostname
  console.log(`Connecting to socket at http://${host}:3001`)
  const socket = io({
    path: '/entradas/socket-proxy.php',
    transports: ['polling'],
    upgrade: false,
    rememberUpgrade: false,
    reconnection: true,
    reconnectionDelay: 500,
    reconnectionAttempts: Infinity
  })

  socket.on('connect', () => {
    console.log('✅ Socket connected (via Proxy Bridge)')
  })
  socket.on('connect_error', (error) => console.error('❌ Socket error:', error))

  return {
    provide: {
      socket
    }
  }
})
