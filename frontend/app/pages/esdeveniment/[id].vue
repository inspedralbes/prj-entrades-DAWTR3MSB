<template>
  <div class="esdeveniment-page">
    <div class="info-header">
      <h1>Concert TR3 Final - Selecció de seients</h1>
      <p class="status">Socket: <span class="connected">Desconnectat (Dia 2)</span></p>
    </div>

    <div class="stage">ESCENARI</div>

    <div class="seats-grid">
      <div 
        v-for="seat in 50" 
        :key="seat" 
        :class="['seat', getSeatStatus(seat)]"
      >
        {{ seat }}
      </div>
    </div>
    
    <div class="leyenda">
      <div class="legend-item"><div class="seat lliure"></div> Lliure</div>
      <div class="legend-item"><div class="seat reservat"></div> Reservat</div>
      <div class="legend-item"><div class="seat venut"></div> Venut</div>
    </div>
  </div>
</template>

<script setup>
// Mock estats per visualització de Dia 4
const getSeatStatus = (seat) => {
  if (seat % 5 === 0) return 'venut';
  if (seat % 7 === 0) return 'reservat';
  return 'lliure';
};
</script>

<style scoped>
.info-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}
.connected { color: #9CA3AF; }

.stage {
  background-color: #374151;
  text-align: center;
  padding: 1rem;
  border-radius: 8px;
  font-weight: bold;
  letter-spacing: 0.5rem;
  margin-bottom: 3rem;
  width: 100%;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.seats-grid {
  display: grid;
  grid-template-columns: repeat(10, 1fr);
  gap: 10px;
  max-width: 800px;
  margin: 0 auto;
  justify-items: center;
}

.seat {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px 6px 20px 20px;
  font-size: 0.8rem;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s;
  color: transparent;
}
.seat:hover {
  transform: scale(1.1);
  color: white;
}
.lliure {
  background-color: #10B981; /* Verd */
}
.reservat {
  background-color: #F59E0B; /* Taronja */
  cursor: not-allowed;
}
.venut {
  background-color: #EF4444; /* Vermell */
  cursor: not-allowed;
}

.leyenda {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-top: 3rem;
}
.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.legend-item .seat {
  width: 20px;
  height: 20px;
  cursor: default;
}
</style>
