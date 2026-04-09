<template>
  <div class="esdeveniment-page">
    <!-- Full-page loading overlay that doesn't unmount the page -->
    <div v-if="store.loading" class="loading-overlay-v2">
      <div class="spinner"></div>
      <p>Consultant disponibilitat...</p>
    </div>

    <!-- Error state -->
    <div v-if="store.error" class="error-container">
      <div class="error-card glassmorphism">
        <p>⚠️ {{ store.error }}</p>
        <button @click="back" class="btn-secondary">Tornar a la cartellera</button>
      </div>
    </div>

    <!-- Content (always mounted but hidden if loading/error) -->
    <div v-show="store.esdeveniment" :class="{ 'content-blur': store.loading }" class="event-main-content">
      <!-- Sophisticated Header with Backdrop Image -->
      <header class="event-header" :style="{ backgroundImage: store.esdeveniment ? `url(${store.esdeveniment.imatge_url})` : 'none' }">
        <div class="header-overlay"></div>
        <div class="header-content">
          <div class="header-main">
            <button @click="back" class="btn-back">←</button>
            <div v-if="store.esdeveniment">
              <h1 class="event-title">{{ store.esdeveniment.nom }}</h1>
              <p class="event-meta">
                <span class="meta-item">📍 {{ store.esdeveniment.recinte }}</span>
                <span class="meta-item">📅 {{ formatDate(store.esdeveniment.data_hora) }}</span>
              </p>
            </div>
          </div>
          <ClientOnly>
            <div class="connection-status" :title="store.socketConectat ? 'Sincronitzat' : 'Desconnectat'">
              <span class="led" :class="{ 'led-on': store.socketConectat }"></span>
              <span class="status-text">{{ store.socketConectat ? 'Llest (Real-time)' : 'Sincronitzant...' }}</span>
            </div>
          </ClientOnly>
        </div>
      </header>

    <div class="cinema-view">
      <div class="stage-container">
        <div class="stage">PANTALLA / ESCENARI</div>
        <div class="stage-glow"></div>
      </div>

      <div class="seats-container">
        <div class="seats-grid">
          <div 
            v-for="seat in store.seients" 
            :key="seat.id" 
            :class="['seat', seat.estat, { 'is-my-selection': store.isSeatSelected(seat.id) }]"
            @click="reservar(seat)"
          >
            <div class="seat-headrest"></div>
            <div class="seat-cushion">{{ seat.numero }}</div>
            
            <!-- Tooltip dynamic -->
            <div class="seat-tooltip">
              Fila {{ seat.fila }} · Núm {{ seat.numero }}
              <br><strong>{{ seat.preu }}€</strong>
            </div>
          </div>
        </div>
      </div>
      
      <div class="legend-pills">
        <div class="pill"><span class="dot lliure"></span> Lliure</div>
        <div class="pill"><span class="dot reservat"></span> Reservat</div>
        <div class="pill"><span class="dot venut"></span> Venut</div>
        <div class="pill"><span class="dot is-my-selection"></span> La teva tria</div>
      </div>
    </div>

    <!-- Selection Bar (Bottom UI) -->
    <ClientOnly>
      <Transition name="slide-up">
        <div v-if="store.selectedSeats.length > 0" class="selection-bar glassmorphism">
          <div class="selection-details">
            <div class="selection-icon">🎟️</div>
            <div class="selection-text">
              <h4>Has triat {{ store.selectedSeats.length }} {{ store.selectedSeats.length === 1 ? 'seient' : 'seients' }}</h4>
              <p>{{ store.selectedSeats.map(s => s.numero).join(', ') }} · {{ formatTimer(store.timer) }} restants</p>
            </div>
          </div>
          <div class="selection-actions">
            <div class="price-display">
              <span class="label">Total</span>
              <span class="value">{{ store.totalPrice }}€</span>
            </div>
            <button @click="openCheckout" class="btn-continue">
              Continuar per pagar
              <span class="icon">→</span>
            </button>
          </div>
        </div>
      </Transition>
    </ClientOnly>

    <!-- Final Checkout Modal -->
    <Transition name="fade">
      <div v-if="showCheckoutForm" class="checkout-overlay" @click.self="showCheckoutForm = false">
        <div class="checkout-card glassmorphism-dark">
          <button class="btn-close" @click="showCheckoutForm = false">×</button>
          
          <div class="checkout-header">
            <h2>Finalitzar Compra</h2>
            <p>Estàs a un pas d'aconseguir la teva entrada per a <strong>{{ store.esdeveniment.nom }}</strong>.</p>
          </div>
          
          <form @submit.prevent="confirmarCompra" class="checkout-form">
            <div class="form-group">
              <label>Nom i Cognoms</label>
              <input v-model="form.name" type="text" placeholder="Ex: Marc Ros" required />
            </div>
            <div class="form-group">
              <label>Correu Electrònic</label>
              <input v-model="form.email" type="email" placeholder="hola@exemple.cat" required />
            </div>
            
            <div class="order-summary">
              <div v-for="s in store.selectedSeats" :key="s.id" class="summary-line">
                <span>Entrada General (Seient {{ s.numero }})</span>
                <span>{{ s.preu }}€</span>
              </div>
              <div class="summary-line total">
                <span>Total a pagar</span>
                <span>{{ store.totalPrice }}€</span>
              </div>
            </div>

            <button type="submit" class="btn-confirm" :disabled="buying">
              {{ buying ? 'Processant...' : 'Confirmar Pagament' }}
            </button>
            
            <p class="security-note">🔒 Transacció segura de TR3</p>
          </form>
        </div>
      </div>
    </Transition>

    <!-- SUCCESS TICKET MODAL -->
    <Transition name="fade">
      <div v-if="showSuccessTicket" class="checkout-overlay success-ticket-view">
        <div class="ticket-container card-anim">
          <div class="ticket-header">
            <div class="success-icon">✔️</div>
            <h2>Compra Confirmada!</h2>
            <p>T'hem enviat el resguard a <strong>{{ form.email }}</strong></p>
          </div>

          <div id="printable-ticket" class="ticket-body">
            <div class="ticket-event-info">
              <h3>{{ store.esdeveniment.nom }}</h3>
              <p class="ticket-meta">📍 {{ store.esdeveniment.recinte }}</p>
              <p class="ticket-meta">📅 {{ formatDate(store.esdeveniment.data_hora) }}</p>
              <div class="ticket-seats">
                <span v-for="s in store.selectedSeats" :key="s.id" class="seat-badge">
                  Seient {{ s.numero }} (Fila {{ s.fila }})
                </span>
              </div>
            </div>

            <div class="qr-area">
              <ClientOnly>
                <qrcode-vue :value="qrValue" :size="200" level="H" background="#ffffff" foreground="#020617" />
              </ClientOnly>
              <p class="qr-help">Presenta aquest codi a l'entrada</p>
            </div>
          </div>

          <div class="ticket-actions">
            <button @click="downloadTicket" class="btn-download">
              📥 Descarregar Entrada
            </button>
            <button @click="addToCalendar" class="btn-calendar">
              🗓️ Google Calendar
            </button>
            <button @click="finishFlow" class="btn-finish">
              Tancar
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</div>
</template>

<script setup>
import QrcodeVue from 'qrcode.vue'
const route = useRoute()
const store = useEventStore()
const { $socket } = useNuxtApp()

const form = ref({ name: '', email: '' })
const buying = ref(false)
const showCheckoutForm = ref(false)
const showSuccessTicket = ref(false)
const qrValue = ref('')

// Carregar estat inicial
const fetchEvent = async () => {
  store.loading = true
  const config = useRuntimeConfig()
  const apiBase = process.server ? config.public.apiBaseInternal : config.public.apiBase
  
  try {
    const data = await $fetch(`${apiBase}/esdeveniments/${route.params.id}`)
    store.setEsdeveniment(data)
    store.error = null
  } catch (e) {
    store.error = "No s'ha pogut connectar amb el servidor. Revisa la base de dades."
  } finally {
    store.loading = false
  }
}

onMounted(() => {
  fetchEvent()

  // Sincronitzar estat inicial si ja està connectat
  if ($socket.connected) store.setSocketStatus(true)

  $socket.on('connect', () => store.setSocketStatus(true))
  $socket.on('disconnect', () => store.setSocketStatus(false))
  
  $socket.on('seient_actualitzat', ({ seientId, estat }) => {
    store.updateSeient(seientId, estat)
  })

  $socket.on('error_concurrencia', ({ message }) => {
    alert(message)
    store.clearReservation()
  })
})

const reservar = (seat) => {
  if (seat.estat !== 'disponible' && !store.isSeatSelected(seat.id)) return
  
  // Emetre al socket el canvi (si el seleccionem el posem en reservat, si el treiem el posem en lliure)
  // Nota: El servidor de sockets ha de permetre alliberar si som nosaltres.
  const nouEstat = store.isSeatSelected(seat.id) ? 'disponible' : 'reservat'
  
  $socket.emit('reservar_seient', { 
    seientId: seat.id, 
    esdevenimentId: route.params.id,
    estat: nouEstat // Passem l'estat desitjat
  })
  
  store.toggleSeatSelection(seat)
}

const openCheckout = () => {
  showCheckoutForm.value = true
}

const confirmarCompra = async () => {
  if (store.selectedSeats.length === 0) return
  
  buying.value = true
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  try {
    const data = await $fetch(`${apiBase}/purchase`, {
      method: 'POST',
      body: {
        seients_ids: store.selectedSeats.map(s => s.id),
        customer_name: form.value.name,
        customer_email: form.value.email
      }
    })
    
    // Generar valor del QR (ID de la primera compra + timestamp)
    qrValue.value = `ORDER-${data.purchases[0].id}-${Date.now()}`
    
    showCheckoutForm.value = false
    showSuccessTicket.value = true
  } catch (e) {
    alert("⚠️ Error en el pagament: " + (e.data?.message || "Torna-ho a intentar."))
  } finally {
    buying.value = false
  }
}

const downloadTicket = async () => {
  if (!process.client) return
  const html2pdf = (await import('html2pdf.js')).default
  const element = document.getElementById('printable-ticket')
  const opt = {
    margin: 0.5,
    filename: `tiquet-huevostyle-${Date.now()}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2, useCORS: true },
    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
  }
  
  html2pdf().set(opt).from(element).save()
}

const addToCalendar = () => {
  const event = store.esdeveniment
  const start = event.data_hora.replace(/[-:]/g, '').split('.')[0] + 'Z'
  const url = `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(event.nom)}&dates=${start}/${start}&details=${encodeURIComponent('Entrada comprada a Huevostyle')}&location=${encodeURIComponent(event.recinte)}`
  window.open(url, '_blank')
}

const finishFlow = () => {
  showSuccessTicket.value = false
  store.clearSelectedSeats()
  useRouter().push('/')
}

const formatTimer = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('ca-ES', {
    day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}

const back = () => useRouter().push('/')
</script>

<style scoped>
.esdeveniment-page {
  min-height: 100vh;
  background-color: #020617;
  color: #f8fafc;
  padding-bottom: 120px; /* Espai per la barra inferior */
}

/* Header */
.event-header {
  height: 350px;
  position: relative;
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: flex-end;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.header-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(to top, #020617 5%, rgba(2, 6, 23, 0.4) 60%, rgba(2, 6, 23, 0.2) 100%);
  backdrop-filter: blur(1px);
}

.header-content {
  position: relative;
  z-index: 2;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 3rem 4rem;
}

.header-main {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.btn-back {
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.2);
  color: white;
  width: 55px;
  height: 55px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.4rem;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.btn-back:hover { 
  background: #3b82f6; 
  border-color: #60a5fa;
  transform: scale(1.1) translateX(-5px);
}

.event-title { font-size: 3rem; font-weight: 900; letter-spacing: -0.04em; text-shadow: 0 4px 20px rgba(0,0,0,0.6); }
.event-meta { color: #cbd5e1; font-size: 1.1rem; margin-top: 0.5rem; display: flex; gap: 2rem; font-weight: 500; }

/* Status LED */
.connection-status {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: rgba(0,0,0,0.5);
  backdrop-filter: blur(10px);
  padding: 0.75rem 1.5rem;
  border-radius: 40px;
  border: 1px solid rgba(255,255,255,0.1);
}

.led {
  width: 12px;
  height: 12px;
  background-color: #ef4444;
  border-radius: 50%;
  box-shadow: 0 0 10px #ef4444;
  transition: all 0.5s ease;
}
.led-on {
  background-color: #22c55e;
  box-shadow: 0 0 15px #22c55e, 0 0 30px rgba(34, 197, 94, 0.6);
}
.status-text { font-size: 0.85rem; font-weight: 800; color: #f8fafc; text-transform: uppercase; letter-spacing: 0.05em; }

/* Content States */
.loading-overlay-v2 {
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(2, 6, 23, 0.8);
  backdrop-filter: blur(8px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
}

.content-blur {
  filter: blur(10px);
  pointer-events: none;
  opacity: 0.5;
  transition: all 0.4s ease;
}

.event-main-content {
  transition: all 0.6s ease;
}

/* Cinema Area */
.cinema-view {
  max-width: 900px;
  margin: 0 auto;
  padding: 2rem;
}

.stage-container {
  margin-bottom: 5rem;
  perspective: 1000px;
}

.stage {
  height: 10px;
  background: #334155;
  border-radius: 50%;
  transform: rotateX(60deg);
  text-align: center;
  font-weight: 800;
  font-size: 0.8rem;
  letter-spacing: 1rem;
  color: #94a3b8;
  padding-top: 20px;
}

.stage-glow {
  height: 100px;
  background: radial-gradient(ellipse at center, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
  margin-top: -40px;
}

.seats-grid {
  display: grid;
  grid-template-columns: repeat(10, 50px);
  gap: 15px;
  justify-content: center;
}

.seat {
  width: 50px;
  height: 55px;
  position: relative;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.seat-headrest {
  width: 35px;
  height: 12px;
  background: #334155;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
  margin-bottom: 2px;
  box-shadow: inset 0 2px 2px rgba(255,255,255,0.1);
}

.seat-cushion {
  width: 100%;
  flex: 1;
  background: #1e293b;
  border-radius: 4px 4px 12px 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: bold;
  color: #64748b;
  box-shadow: inset 0 -4px 0 rgba(0,0,0,0.3);
}

.seat:hover { transform: translateY(-8px) scale(1.1); z-index: 10; }
.seat:hover .seat-tooltip { opacity: 1; transform: translateX(-50%) translateY(-10px); }

/* Seat Tooltip */
.seat-tooltip {
  position: absolute;
  bottom: 110%;
  left: 50%;
  transform: translateX(-50%) translateY(0);
  background: #ffffff;
  color: #020617;
  padding: 0.6rem 1rem;
  border-radius: 8px;
  font-size: 0.75rem;
  white-space: nowrap;
  pointer-events: none;
  opacity: 0;
  transition: all 0.3s;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5);
  font-weight: 500;
}
.seat-tooltip::after {
  content: '';
  position: absolute;
  top: 100%; left: 50%;
  transform: translateX(-50%);
  border: 6px solid transparent;
  border-top-color: #ffffff;
}

/* Seat States */
.lliure .seat-headrest { background: #34d399; }
.lliure .seat-cushion { background: #059669; color: #ecfdf5; }

.reservat .seat-headrest { background: #fbbf24; }
.reservat .seat-cushion { background: #d97706; color: #fffbeb; cursor: not-allowed; }

.venut .seat-headrest { background: #f87171; }
.venut .seat-cushion { background: #dc2626; color: #fef2f2; cursor: not-allowed; }

.is-my-selection .seat-headrest { background: #60a5fa !important; }
.is-my-selection .seat-cushion { 
  background: #2563eb !important; 
  color: white !important; 
  box-shadow: 0 0 20px rgba(37, 99, 235, 0.6) !important;
  border: 2px solid white !important;
}

/* Legends */
.legend-pills {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 5rem;
  flex-wrap: wrap;
}

.pill {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: rgba(255,255,255,0.03);
  padding: 0.6rem 1.2rem;
  border-radius: 40px;
  font-size: 0.85rem;
  color: #94a3b8;
  border: 1px solid rgba(255,255,255,0.05);
}

.dot { width: 12px; height: 12px; border-radius: 50%; }
.dot.lliure { background: #10b981; }
.dot.reservat { background: #f59e0b; }
.dot.venut { background: #ef4444; }
.dot.my-selection { background: #3b82f6; border: 1px solid white; }

/* Selection Bar */
.selection-bar {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  height: 100px;
  padding: 0 4rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 100;
  border-top: 1px solid rgba(255,255,255,0.1);
}

.selection-details {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.selection-icon { font-size: 2.5rem; }
.selection-text h4 { font-size: 1.2rem; font-weight: bold; }
.selection-text p { color: #94a3b8; font-size: 0.9rem; margin-top: 0.2rem; }

.selection-actions {
  display: flex;
  align-items: center;
  gap: 3rem;
}

.price-display { display: flex; flex-direction: column; text-align: right; }
.price-display .label { font-size: 0.7rem; text-transform: uppercase; color: #64748b; letter-spacing: 1px; }
.price-display .value { font-size: 2rem; font-weight: 800; color: #3b82f6; }

.btn-continue {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 1.2rem 2.5rem;
  border-radius: 12px;
  font-weight: 800;
  font-size: 1.1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s;
  box-shadow: 0 10px 30px -10px rgba(59, 130, 246, 0.5);
}
.btn-continue:hover { background: #2563eb; transform: scale(1.05); }

/* Checkout Modal */
.checkout-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(2, 6, 23, 0.9);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1.5rem;
}

.glassmorphism-dark {
  background: #0f172a;
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.8);
}

.checkout-card {
  width: 100%;
  max-width: 500px;
  padding: 3rem;
  border-radius: 30px;
  position: relative;
}

.btn-close {
  position: absolute;
  top: 1.5rem; right: 1.5rem;
  background: none; border: none;
  color: #94a3b8; font-size: 2rem;
  cursor: pointer;
}

.checkout-header h2 { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; }
.checkout-header p { color: #94a3b8; line-height: 1.5; }

.checkout-form { margin-top: 2.5rem; display: flex; flex-direction: column; gap: 1.5rem; }

.form-group { display: flex; flex-direction: column; gap: 0.6rem; }
.form-group label { font-size: 0.8rem; font-weight: bold; color: #64748b; text-transform: uppercase; }
.form-group input {
  background: #020617;
  border: 1px solid #334155;
  padding: 1rem;
  border-radius: 12px;
  color: white;
  font-size: 1.1rem;
  transition: border-color 0.3s;
}
.form-group input:focus { outline: none; border-color: #3b82f6; }

.order-summary {
  background: rgba(255,255,255,0.03);
  padding: 1.5rem;
  border-radius: 15px;
  margin: 1rem 0;
}

.summary-line {
  display: flex;
  justify-content: space-between;
  font-size: 0.9rem;
  color: #94a3b8;
  margin-bottom: 0.5rem;
}

.summary-line.total {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(255,255,255,0.1);
  font-weight: 800;
  font-size: 1.2rem;
  color: white;
}

.btn-confirm {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  padding: 1.2rem;
  border-radius: 12px;
  font-weight: 800;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
}
.btn-confirm:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.6); }

.security-note { text-align: center; font-size: 0.75rem; color: #475569; margin-top: 1rem; }

/* Transitions */
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.4s ease-out; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(100px); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.glassmorphism {
  background: rgba(15, 23, 42, 0.8);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}

.loading-overlay {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  background: #020617;
}

.spinner {
  width: 50px; height: 50px;
  border: 5px solid rgba(59, 130, 246, 0.1);
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s infinite linear;
  margin-bottom: 1.5rem;
}
@keyframes spin { to { transform: rotate(360deg); } }

.error-container {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}
.error-card { padding: 3rem; text-align: center; border-radius: 20px; }
.btn-secondary {
  margin-top: 2rem;
  background: #334155;
  color: white;
  border: none;
  padding: 0.8rem 2rem;
  border-radius: 8px;
  cursor: pointer;
}
.btn-finish {
  background: transparent;
  color: #94a3b8;
  border: 1px solid #334155;
  padding: 0.8rem 2rem;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}
.btn-finish:hover { background: rgba(255,255,255,0.05); color: white; }

/* Styles for the Premium Ticket */
.success-ticket-view { z-index: 2000; }
.card-anim { animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1); }

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(40px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}

.ticket-container {
  background: #ffffff;
  color: #020617;
  width: 100%;
  max-width: 500px;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 40px 100px rgba(0,0,0,0.5);
}

.ticket-header {
  background: #22c55e;
  padding: 2.5rem;
  text-align: center;
  color: white;
}

.success-icon { font-size: 3rem; margin-bottom: 1rem; }
.ticket-header h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem; }
.ticket-header p { opacity: 0.9; font-size: 0.95rem; }

.ticket-body {
  padding: 2.5rem;
  background: white;
  position: relative;
}

.ticket-event-info h3 { font-size: 1.5rem; font-weight: 800; margin-bottom: 1rem; color: #020617; }
.ticket-meta { color: #64748b; font-weight: 500; margin-bottom: 0.4rem; font-size: 0.95rem; }

.ticket-seats { margin-top: 1.5rem; display: flex; flex-wrap: wrap; gap: 0.5rem; }
.seat-badge {
  background: #f1f5f9;
  color: #334155;
  padding: 0.4rem 0.8rem;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
}

.qr-area {
  margin-top: 2.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding-top: 2rem;
  border-top: 2px dashed #e2e8f0;
}

.qr-help { color: #94a3b8; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

.ticket-actions {
  padding: 2rem 2.5rem 3rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background: #f8fafc;
}

.btn-download {
  background: #020617;
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
}
.btn-download:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

.btn-calendar {
  background: #ffffff;
  color: #020617;
  border: 1px solid #e2e8f0;
  padding: 1rem;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
}
.btn-calendar:hover { background: #f1f5f9; }

@media print {
  /* Ocultem tot el contingut de la pàgina */
  body * { visibility: hidden; }
  /* Fem visible només el tiquet i el seu contingut */
  .success-ticket-view, 
  .success-ticket-view * { visibility: visible; }
  /* Posicionem el tiquet al principi de la pàgina d'impressió */
  .success-ticket-view {
    position: absolute;
    left: 0; top: 0;
    width: 100%;
    margin: 0; padding: 0;
    background: white !important;
    display: block !important;
  }
  .ticket-container {
    box-shadow: none !important;
    border: none !important;
    margin: 0 auto;
    width: 100%;
    max-width: 100% !important;
  }
  .ticket-actions, .btn-close { display: none !important; }
}
</style>
