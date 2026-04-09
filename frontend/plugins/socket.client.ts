import { io } from 'socket.io-client'

export default defineNuxtPlugin(() => {
  if (process.server) return

  const host = window.location.hostname
  console.log(`Connecting to socket at http://${host}:3001`)
  const socket = io(`http://${host}:3001`, {
    reconnection: true,
    reconnectionDelay: 1000,
    reconnectionAttempts: Infinity
  })

  socket.on('connect', () => console.log('✅ Socket connected'))
  socket.on('connect_error', (error) => console.error('❌ Socket error:', error))

  return {
    provide: {
      socket
    }
  }
})
