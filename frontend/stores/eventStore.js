import { defineStore } from 'pinia'

export const useEventStore = defineStore('event', {
  state: () => ({
    esdeveniment: null,
    seients: [],
    loading: false,
    error: null,
    socketConectat: false,
    selectedSeats: [], // Array de seients seleccionats
    timer: 180, // 3 minuts
    timerInterval: null
  }),

  getters: {
    totalPrice: (state) => {
      const total = state.selectedSeats.reduce((sum, seat) => sum + Number(seat.preu || 0), 0)
      return total.toFixed(2)
    },
    isSeatSelected: (state) => (seatId) => state.selectedSeats.some(s => s.id === seatId)
  },

  actions: {
    setEsdeveniment(data) {
      this.esdeveniment = data
      this.seients = data.seients
    },

    updateSeient(seientId, estat) {
      const index = this.seients.findIndex(s => s.id === seientId)
      if (index !== -1) {
        this.seients[index].estat = estat
        
        // Si el seient que s'ha actualitzat externa-ment (venut/reservat per un altre) era a la nostra tria
        if (this.isSeatSelected(seientId) && estat !== 'reservat') {
          // Només el treiem si no som nosaltres qui el tenim (però el socket no distingeix qui, 
          // axí que confiem en la lògica de que si algú l'actualitza, l'hem de perdre si no som "els amos")
          // En realitat, el socket només emet si CANVIA l'estat.
          this.selectedSeats = this.selectedSeats.filter(s => s.id !== seientId)
          if (this.selectedSeats.length === 0) this.stopTimer()
        }
      }
    },

    toggleSeatSelection(seat) {
      const index = this.selectedSeats.findIndex(s => s.id === seat.id)
      if (index !== -1) {
        // Ja estava seleccionat, el desmarquem
        this.selectedSeats.splice(index, 1)
        if (this.selectedSeats.length === 0) {
          this.stopTimer()
        }
      } else {
        // Nou seient seleccionat
        if (this.selectedSeats.length === 0) {
          this.startTimer()
        }
        this.selectedSeats.push(seat)
      }
    },

    clearSelectedSeats() {
      this.selectedSeats = []
      this.stopTimer()
      this.timer = 180
    },

    setSocketStatus(status) {
      this.socketConectat = status
    },

    startTimer() {
      this.stopTimer()
      this.timer = 180
      this.timerInterval = setInterval(() => {
        if (this.timer > 0) {
          this.timer--
        } else {
          this.stopTimer()
          // Emetem l'alliberament si calgués, o deixem que el servidor ho faci per timeout
          this.clearSelectedSeats()
          if (process.client) {
            alert("⏰ S'ha acabat el temps de reserva (3 minuts).")
          }
        }
      }, 1000)
    },

    stopTimer() {
      if (this.timerInterval) {
        clearInterval(this.timerInterval)
        this.timerInterval = null
      }
    }
  }
})
