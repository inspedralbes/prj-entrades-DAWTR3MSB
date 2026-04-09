<template>
  <div class="admin-page">
    <div class="admin-header">
      <NuxtLink to="/" class="btn-home">← Tornar a la web</NuxtLink>
      <h1 class="glow-text">Panell de Control</h1>
      <p class="subtitle">Gestiona esdeveniments i visualitza el rendiment en temps real.</p>
    </div>

    <div v-if="!authorized" class="auth-container">
      <div class="auth-card glassmorphism-dark zoom-in">
        <div class="lock-icon">🔒</div>
        <h2>Accés Restringit</h2>
        <p>Introdueix la clau mestra del sistema.</p>
        <div class="input-glow-group">
          <input v-model="pass" type="password" placeholder="••••••••" @keyup.enter="login" />
          <button @click="login" class="btn-login">Desbloquejar</button>
        </div>
        <p class="hint">Pista: admin123</p>
      </div>
    </div>

    <div v-if="authorized" class="admin-content">
      <!-- Stats Dashboard -->
      <section class="dashboard-section">
        <div class="stats-grid">
          <div class="stat-card glassmorphism-dark">
            <span class="stat-label">Recaptació Total</span>
            <span class="stat-value">{{ stats.total_revenue }}€</span>
            <div class="stat-bar revenue"></div>
          </div>
          <div class="stat-card glassmorphism-dark">
            <span class="stat-label">Entrades Venudes</span>
            <span class="stat-value">{{ stats.total_sold }}</span>
            <div class="stat-bar sold"></div>
          </div>
          <div class="stat-card glassmorphism-dark">
            <span class="stat-label">Ocupació Mitjana</span>
            <span class="stat-value">{{ stats.occupancy_percent }}%</span>
            <div class="stat-bar occupancy" :style="{ width: stats.occupancy_percent + '%' }"></div>
          </div>
        </div>
      </section>

      <div class="admin-grid-two-cols">
        <!-- Event Management & List -->
        <section class="management-section">
          <div class="creation-card glassmorphism-dark">
            <div class="card-header">
              <div class="icon-circle">➕</div>
              <h3>Gestionar Cartellera</h3>
            </div>
            
            <form @submit.prevent="createEvent" class="admin-form">
              <div class="form-group">
                <label>Nom de l'Acte</label>
                <input v-model="newEvent.nom" type="text" placeholder="Ex: Festival de Rock" required />
              </div>
              <div class="form-group">
                <label>Data i Hora</label>
                <input v-model="newEvent.data_hora" type="datetime-local" required />
              </div>
              <div class="form-group">
                <label>Aforament</label>
                <input v-model="newEvent.aforament" type="number" min="1" max="200" required />
              </div>
              <button type="submit" class="btn-create" :disabled="loading">
                Publicar Esdeveniment
              </button>
            </form>

            <div class="events-list-mini">
              <h4>Esdeveniments Actius</h4>
              <div v-for="ev in stats.events_detail" :key="ev.id" class="mini-event-item">
                <span>{{ ev.nom }}</span>
                <span class="badge" :class="ev.occupancy > 80 ? 'high' : ''">{{ ev.occupancy }}%</span>
              </div>
            </div>
          </div>
        </section>

        <!-- Recent Sales -->
        <section class="sales-section">
          <div class="sales-card glassmorphism-dark">
            <div class="card-header">
              <div class="icon-circle">🧾</div>
              <h3>Últimes Vendes</h3>
            </div>
            <div class="sales-table-wrapper">
              <table class="sales-table">
                <thead>
                  <tr>
                    <th>Client</th>
                    <th>Esdeveniment</th>
                    <th>Import</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="sale in stats.recent_sales" :key="sale.id">
                    <td>{{ sale.customer_name }}</td>
                    <td>{{ sale.event_nom }}</td>
                    <td>{{ sale.amount }}€</td>
                  </tr>
                </tbody>
              </table>
              <p v-if="!stats.recent_sales?.length" class="empty-msg">No hi ha vendes recents.</p>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
const authorized = ref(false)
const pass = ref('')
const stats = ref({ 
  total_revenue: 0, 
  total_sold: 0, 
  occupancy_percent: 0, 
  events_detail: [],
  recent_sales: []
})
const loading = ref(false)

const newEvent = ref({
  nom: '',
  data_hora: '',
  recinte: 'Auditori Principal',
  aforament: 50
})

const login = () => {
  if (pass.value === 'admin123') {
    authorized.value = true
    fetchStats()
  } else {
    alert("Clau incorrecta.")
  }
}

const fetchStats = async () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase
  try {
    const data = await $fetch(`${apiBase}/admin/stats`)
    stats.value = data
  } catch (e) {
    console.error("Error stats")
  }
}

const createEvent = async () => {
  loading.value = true
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase
  try {
    await $fetch(`${apiBase}/admin/events`, {
      method: 'POST',
      body: newEvent.value
    })
    alert("Esdeveniment creat!")
    newEvent.value = { nom: '', data_hora: '', recinte: 'Auditori Principal', aforament: 50 }
    fetchStats()
  } catch (e) {
    alert("Error creant l'esdeveniment.")
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.admin-page {
  min-height: 100vh;
  background-color: #020617;
  color: #f8fafc;
  padding: 4rem 2rem;
}

.admin-header {
  text-align: center;
  margin-bottom: 5rem;
  position: relative;
}

.btn-home {
  position: absolute;
  top: -2rem; left: 0;
  color: #64748b;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s;
}
.btn-home:hover { color: #3b82f6; }

.glow-text {
  font-size: 3.5rem;
  font-weight: 900;
  background: linear-gradient(to right, #3b82f6, #60a5fa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.4));
  margin-bottom: 0.5rem;
}

.subtitle { color: #94a3b8; font-size: 1.1rem; }

/* Auth */
.auth-container {
  display: flex;
  justify-content: center;
  margin-top: 4rem;
}

.auth-card {
  width: 100%;
  max-width: 450px;
  padding: 3rem;
  border-radius: 30px;
  text-align: center;
}

.lock-icon { font-size: 3rem; margin-bottom: 1.5rem; }
.auth-card h2 { font-size: 2rem; margin-bottom: 1rem; }
.auth-card p { color: #94a3b8; margin-bottom: 2rem; }

.input-glow-group {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.input-glow-group input {
  background: #020617;
  border: 1px solid #334155;
  padding: 1rem;
  border-radius: 12px;
  color: white;
  text-align: center;
  font-size: 1.2rem;
  transition: all 0.3s;
}
.input-glow-group input:focus { border-color: #3b82f6; box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); outline: none; }

.btn-login {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 12px;
  font-weight: 800;
  cursor: pointer;
}

.hint { margin-top: 1.5rem; font-size: 0.8rem; opacity: 0.5; }

/* Dashboard */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 5rem;
}

.stat-card {
  padding: 2.5rem;
  border-radius: 24px;
  position: relative;
  overflow: hidden;
}

.stat-label { display: block; color: #64748b; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem; }
.stat-value { font-size: 3rem; font-weight: 900; color: #f8fafc; }
.stat-value.pulse { color: #f59e0b; animation: pulseGlow 2s infinite; }

.stat-bar {
  position: absolute;
  bottom: 0; left: 0;
  height: 4px;
  background: #334155;
}
.stat-bar.revenue { background: #10b981; width: 100%; }
.stat-bar.sold { background: #3b82f6; width: 100%; }
.stat-bar.occupancy { background: #a855f7; }
.stat-bar.reservations { background: #f59e0b; width: 100%; }

/* Management */
.creation-card {
  padding: 4rem;
  border-radius: 32px;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 3rem;
}

.icon-circle {
  width: 60px; height: 60px;
  background: rgba(59, 130, 246, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.card-header h3 { font-size: 2rem; margin-bottom: 0.2rem; }
.card-header p { color: #64748b; }

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-bottom: 3rem;
}

.form-group { display: flex; flex-direction: column; gap: 0.8rem; }
.form-group label { font-size: 0.85rem; font-weight: 800; color: #475569; text-transform: uppercase; }
.form-group input {
  background: #020617;
  border: 1px solid #1e293b;
  padding: 1.2rem;
  border-radius: 12px;
  color: white;
  font-size: 1rem;
}

.btn-create {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  border: none;
  padding: 1.5rem;
  border-radius: 16px;
  font-weight: 800;
  font-size: 1.1rem;
  cursor: pointer;
  width: 100%;
  transition: all 0.3s;
  box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
}
.btn-create:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -10px rgba(59, 130, 246, 0.6); }

/* Admin Grid */
.admin-grid-two-cols {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 2rem;
  align-items: start;
}

.events-list-mini { margin-top: 3rem; border-top: 1px solid #1e293b; padding-top: 2rem; }
.mini-event-item {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem; background: rgba(0,0,0,0.2); border-radius: 12px; margin-bottom: 0.8rem;
}
.badge { font-size: 0.75rem; padding: 0.3rem 0.6rem; border-radius: 20px; background: #3b82f6; font-weight: 800; }
.badge.high { background: #ef4444; }

.sales-table-wrapper { margin-top: 2rem; overflow-x: auto; }
.sales-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
.sales-table th { text-align: left; padding: 1rem; color: #64748b; border-bottom: 2px solid #1e293b; }
.sales-table td { padding: 1rem; border-bottom: 1px solid #1e293b; color: #94a3b8; }
.empty-msg { text-align: center; color: #475569; padding: 3rem; font-style: italic; }

.glassmorphism-dark {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.05);
}

@keyframes pulseGlow {
  0% { text-shadow: 0 0 0px rgba(245, 158, 11, 0); }
  50% { text-shadow: 0 0 20px rgba(245, 158, 11, 0.5); }
  100% { text-shadow: 0 0 0px rgba(245, 158, 11, 0); }
}

.zoom-in { animation: zoomIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes zoomIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
</style>
