<template>
  <div class="portada">
    <h1>Llistat d'Esdeveniments VIP</h1>
    <div v-if="pending">Carregant esdeveniments...</div>
    <div v-else-if="error">Error al carregar les dades. Revisa que el backend i la BD estiguin actius.</div>
    <div v-else class="events-grid">
      <div v-for="event in esdeveniments" :key="event.id" class="event-card">
        <h3>{{ event.nom }}</h3>
        <p>Data: {{ new Date(event.data_hora).toLocaleDateString() }}</p>
        <p>Recinte: {{ event.recinte }}</p>
        <NuxtLink :to="'/esdeveniment/' + event.id" class="btn-primary">Comprar Entrades</NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()
const apiBase = config.public.apiBase || 'http://localhost:3001/api'
const { data: esdeveniments, pending, error } = useFetch(`${apiBase}/esdeveniments`)
</script>

<style scoped>
.portada h1 {
  font-size: 2.5rem;
  margin-bottom: 2rem;
}
.events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}
.event-card {
  background-color: #1F2937;
  padding: 1.5rem;
  border-radius: 8px;
  border: 1px solid #374151;
  transition: transform 0.2s;
}
.event-card:hover {
  transform: translateY(-5px);
  border-color: #3B82F6;
}
.btn-primary {
  display: inline-block;
  margin-top: 1rem;
  background-color: #3B82F6;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
}
.btn-primary:hover {
  background-color: #2563EB;
}
</style>
