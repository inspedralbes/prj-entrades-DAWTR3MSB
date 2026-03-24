<template>
  <div class="portada">
    <h1>Llistat d'Esdeveniments VIP</h1>
    <div class="events-grid">
      <div class="event-card">
        <h3>Concert TR3 Final</h3>
        <p>Data: 24 de Març de 2026</p>
        <p>Recinte: Palau Sant Jordi</p>
        <NuxtLink to="/esdeveniment/1" class="btn-primary">Comprar Entrades</NuxtLink>
      </div>
    </div>
  </div>
</template>

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
