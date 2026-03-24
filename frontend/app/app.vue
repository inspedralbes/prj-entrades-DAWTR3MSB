<template>
  <div class="app-container">
    <header class="main-header">
      <h2>Entradas huevostyle</h2>
    </header>
    <main>
      <NuxtPage />
    </main>
  </div>
</template>

<style>
body {
  margin: 0;
  font-family: 'Inter', system-ui, sans-serif;
  background-color: #111827;
  color: #F9FAFB;
}
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
.main-header {
  background-color: #1F2937;
  padding: 1rem 2rem;
  border-bottom: 1px solid #374151;
}
.main-header h2 {
  margin: 0;
  color: #3B82F6;
}
main {
  padding: 2rem;
  flex-grow: 1;
}
</style>
