<template>
  <div class="home-page">
    <section class="hero">
      <div class="hero-content">
        <h1 class="glow-text">Entrades huevostyle</h1>
        <p class="hero-subtitle">Reserva la teva experiència en temps real amb zero col·lisions.</p>
      </div>
    </section>

    <main class="content-container">
      <div class="section-header">
        <h2>Pròxims Esdeveniments</h2>
        <NuxtLink to="/admin" class="admin-link">Accés Admin</NuxtLink>
      </div>
      
      <!-- Skeleton Loader while pending (only server-side or if truly pending) -->
      <div v-if="pending" class="event-grid">
        <div v-for="n in 6" :key="n" class="event-card skeleton-card">
          <div class="skeleton-image"></div>
          <div class="skeleton-info">
            <div class="skeleton-line title"></div>
            <div class="skeleton-line desc"></div>
            <div class="skeleton-line footer"></div>
          </div>
        </div>
      </div>
      
      <div v-else-if="error" class="error-card glassmorphism">
        <p>⚠️ Error de connexió. Revisa que el backend estigui actiu.</p>
      </div>

      <div v-show="!pending && esdeveniments" class="event-grid">
        <div v-for="event in esdeveniments" :key="event.id" class="event-card glassmorphism">
          <div class="event-image-container">
            <img :src="event.imatge_url" :alt="event.nom" class="event-img" loading="lazy" />
            <div class="date-tag">
              <span class="day">{{ new Date(event.data_hora).getDate() }}</span>
              <span class="month">{{ getMonthName(event.data_hora) }}</span>
            </div>
            <div class="image-overlay"></div>
          </div>
          <div class="event-info">
            <h3 class="event-name">{{ event.nom }}</h3>
            <p class="event-desc">{{ event.descripcio }}</p>
            <div class="event-footer">
              <span class="venue">📍 {{ event.recinte }}</span>
              <NuxtLink :to="'/esdeveniment/' + event.id" class="btn-tickets">
                Veure Seients
                <span class="arrow">→</span>
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()
const apiBase = process.server ? config.public.apiBaseInternal : config.public.apiBase
const { data: esdeveniments, pending, error } = useFetch(`${apiBase}/esdeveniments`)

const getMonthName = (dateStr) => {
  return new Date(dateStr).toLocaleString('ca-ES', { month: 'short' }).toUpperCase()
}
</script>

<style scoped>
.home-page {
  background-color: #020617;
  color: #f8fafc;
  min-height: 100vh;
}

.hero {
  height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  background: radial-gradient(circle at top, #1e293b 0%, #020617 100%);
  position: relative;
  overflow: hidden;
}

.hero-content {
  position: relative;
  z-index: 2;
  animation: fadeIn 1.2s ease-out;
}

.glow-text {
  font-size: 5rem;
  font-weight: 900;
  background: linear-gradient(to right, #3b82f6, #60a5fa, #a855f7);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  filter: drop-shadow(0 0 20px rgba(59, 130, 246, 0.4));
  margin-bottom: 1.5rem;
  letter-spacing: -0.05em;
}

.hero-subtitle {
  font-size: 1.4rem;
  color: #94a3b8;
  max-width: 700px;
  margin: 0 auto;
  font-weight: 400;
}

.content-container {
  max-width: 1400px;
  margin: -80px auto 0;
  padding: 0 4rem 10rem;
  position: relative;
  z-index: 10;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4rem;
}

.section-header h2 { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em; }

.admin-link {
  font-size: 0.85rem;
  color: #475569;
  text-decoration: none;
  transition: all 0.3s;
  background: rgba(255,255,255,0.03);
  padding: 0.5rem 1.2rem;
  border-radius: 30px;
}
.admin-link:hover { color: #3b82f6; background: rgba(59, 130, 246, 0.1); }

.event-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 3.5rem;
}

.event-card {
  border-radius: 32px;
  overflow: hidden;
  transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  display: flex;
  flex-direction: column;
  border: 1px solid rgba(255,255,255,0.05);
}

.event-card:hover {
  transform: translateY(-15px) scale(1.02);
  box-shadow: 0 30px 60px -12px rgba(0,0,0,0.8), 0 0 30px rgba(59, 130, 246, 0.2);
  border-color: rgba(59, 130, 246, 0.3);
}

.event-image-container {
  height: 240px;
  position: relative;
  background: #0f172a;
}

.event-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.8s cubic-bezier(0.2, 0, 0.2, 1);
}

.event-card:hover .event-img {
  transform: scale(1.1);
}

.image-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(to bottom, transparent 0%, rgba(2, 6, 23, 0.9) 100%);
}

.date-tag {
  position: absolute;
  top: 1.5rem; left: 1.5rem;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  color: #020617;
  padding: 0.75rem 1.25rem;
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: 0 10px 25px rgba(0,0,0,0.4);
  z-index: 2;
  border: 1px solid rgba(255,255,255,0.2);
}

.date-tag .day { font-size: 1.5rem; font-weight: 900; line-height: 1; }
.date-tag .month { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; margin-top: 0.2rem; }

.event-info { 
  padding: 2.5rem; 
  flex: 1; 
  display: flex; 
  flex-direction: column; 
  background: linear-gradient(to bottom, rgba(15, 23, 42, 0.8), #0f172a); 
}

.event-name { font-size: 1.8rem; font-weight: 800; margin-bottom: 1rem; color: #f8fafc; letter-spacing: -0.02em; }
.event-desc { font-size: 1rem; color: #94a3b8; line-height: 1.6; margin-bottom: 2.5rem; flex: 1; opacity: 0.8; }

.event-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.venue { font-size: 0.9rem; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; }

.btn-tickets {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  text-decoration: none;
  padding: 1rem 2rem;
  border-radius: 16px;
  font-weight: 800;
  font-size: 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
}

.btn-tickets:hover { 
  box-shadow: 0 15px 30px -5px rgba(59, 130, 246, 0.6);
  transform: translateX(8px);
}

.glassmorphism {
  background: rgba(30, 41, 59, 0.3);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}

/* Skeletons */
.skeleton-card {
  height: 500px;
  background: #0f172a;
}
.skeleton-image {
  height: 240px;
  background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}
.skeleton-info { padding: 2.5rem; }
.skeleton-line {
  background: #1e293b;
  margin-bottom: 1rem;
  border-radius: 4px;
  animation: loading 1.5s infinite;
}
.skeleton-line.title { height: 2rem; width: 80%; }
.skeleton-line.desc { height: 4rem; width: 100%; }
.skeleton-line.footer { height: 3rem; width: 100%; margin-top: 2rem; }

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }

.error-card {
  padding: 4rem;
  background: rgba(239, 68, 68, 0.05);
  border: 1px solid rgba(239, 68, 68, 0.1);
  border-radius: 24px;
  text-align: center;
  color: #f87171;
  font-weight: 600;
}
</style>
