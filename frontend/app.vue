<template>
  <div class="app-root">
    <NuxtPage />
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap');

:root {
  --bg-deep: #020617;
  --bg-card: #0f172a;
  --accent: #3b82f6;
  --text-main: #f8fafc;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body, #__nuxt, .app-root {
  background-color: #020617 !important;
  color: #f8fafc !important;
  min-height: 100vh;
  font-family: 'Outfit', sans-serif !important;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #020617;
}
::-webkit-scrollbar-thumb {
  background: #1e293b;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #3b82f6;
}

/* Transitions */
.page-enter-active,
.page-leave-active {
  transition: all 0.3s;
}
.page-enter-from,
.page-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
