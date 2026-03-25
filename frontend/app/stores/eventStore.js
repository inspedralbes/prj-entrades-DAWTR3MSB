import { defineStore } from 'pinia'

export const useEventStore = defineStore('event', {
  state: () => ({
    esdeveniment: null,
    seients: [],
    loading: false,
    error: null,
    socketConectat: false
  }),

  actions: {
    setEsdeveniment(data) {
      this.esdeveniment = data
      this.seients = data.seients || []
    },

    updateSeient(seientId, estat) {
      const seient = this.seients.find(s => s.id === seientId)
      if (seient) {
        seient.estat = estat
      }
    },

    setSocketStatus(status) {
      this.socketConectat = status
    }
  }
})
