# Projecte TR3DAW: CompraEntrades - Pla de 10 Dies

Cada dia inclou els passos per programar i l'operació per pujar a GitHub al final del dia.

## Dia 1: Setup Inicial i Repositori
- [ ] Crear el repositori a GitHub amb nom `DAWTR3XXX` (canviant XXX per les teves inicials).
- [ ] Configurar el `README.md` amb la teva informació i documentació requerida.
- [ ] Crear l'estructura de carpetes (`/frontend` i `/backend`).
- [ ] Inicialitzar Node.js al backend (`npm init -y`) i instal·lar Express i Socket.IO.
- [ ] **GitHub:** Fer el primer commit amb l'esquelet de carpetes: `git commit -am "Dia 1: Setup inicial i backend" && git push`.

## Dia 2: Setup Frontend i Client Socket
- [ ] Inicialitzar el frontend amb Nuxt 3 i Vue 3 (`npx nuxi@latest init frontend`).
- [ ] Instal·lar Pinia i el client de Socket.IO (`socket.io-client`).
- [ ] Configurar un `plugin` a Nuxt per connectar el Socket.IO al backend.
- [ ] Verificar que backend i frontend es comuniquen mitjançant un `console.log` de connexió exitosa.
- [ ] **GitHub:** `git commit -am "Dia 2: Setup complet de Nuxt, Pinia i connexió Socket" && git push`.

## Dia 3: Base de Dades i Rutes API
- [ ] Dissenyar l'esquema de Base de Dades (MySQL o Postgres) per Usuaris, Esdeveniments i Seients.
- [ ] Crear l'script SQL de creació i inserir dades falses (1 esdeveniment amb seients lliures).
- [ ] Connectar el backend a la BD i crear una API REST per llistar l'esdeveniment.
- [ ] **GitHub:** `git commit -am "Dia 3: Base de dades enllestida i rutes API bàsiques" && git push`.

## Dia 4: Interfície del Frontend (MVP)
- [ ] Maquetar la portada amb el llistat d'esdeveniments usant Server Side Rendering (SSR) amb Nuxt.
- [ ] Crear la pàgina de detall on mostra la informació general de l'esdeveniment i el mapa de seients.
- [ ] Dibuixar el plànol de seients amb CSS (estats estàtics de moment: Disponible, Venut).
- [ ] **GitHub:** `git commit -am "Dia 4: Interfície de llistat d'esdeveniments i plànol dibuixat" && git push`.

## Dia 5: Gestió de l'Estat amb Pinia
- [ ] Definir a Pinia l'estat global dels seients d'aquell esdeveniment per gestionar els estats en temps real.
- [ ] Carregar els seients des de l'API al carregar la pàgina i guardar-los a Pinia.
- [ ] Fer que l'HTML (el map de seients de Vue) llegeixi els colors i estats directament del store de Pinia.
- [ ] **GitHub:** `git commit -am "Dia 5: Integració de Pinia per pintar l'estat dels seients" && git push`.

## Dia 6: El Nucli del Temps Real I (Reserva)
- [ ] Clicar en un seient al Frontend -> envia event a través de Socket.IO `reservar_seient`.
- [ ] Servidor rep la petició, verifica si està disponible i, si ho està, el bloqueja (Estat = Reservat temporalment).
- [ ] Servidor envia event `seient_posat_a_reservat` a TOTS els usuaris connectats perquè tothom vegi el seient en un altre color.
- [ ] Pinia al Client rep l'event websocket i canvia l'estat del seient al moment (i la UI s'actualitza a l'instant).
- [ ] **GitHub:** `git commit -am "Dia 6: Sincronització asíncrona de reserves iniciada" && git push`.

## Dia 7: El Nucli del Temps Real II (Temporitzador)
- [ ] Afegir un temporitzador visible al client (3-5 min) en fer la reserva per indicar el temps limit.
- [ ] El servidor guarda quan caduca la reserva. Quan caduca, emet a tots l'event `seient_disponible` de nou.
- [ ] Mostrar alertes visuals immediates a l'usuari si clica un seient que acaba de ser reservat per un altre usuari. Es valida tot al servidor.
- [ ] **GitHub:** `git commit -am "Dia 7: Temporitzador de reserva i gestió de conflictes enllestida" && git push`.

## Dia 8: Procés de Compra i Entrades
- [ ] Formulari ràpid de pagament "Checkout". Introduir nom i dades personals.
- [ ] Backend verifica si la teva reserva encara no ha expirat. Si és vàlida, guarda la compra final a la Base de Dades.
- [ ] Seients passen a estat "Venut" permanentment -> enviar esdeveniment `seient_venut` a tothom.
- [ ] Crear Vista per a consultar l'entrada comprada amb seients associats.
- [ ] **GitHub:** `git commit -am "Dia 8: Funcionalitat de pagament i assignació final" && git push`.

## Dia 9: Panell d'Administració
- [ ] Administrador inicia sessió: Pàgina amb visió de l'estat actual en temps real (usuaris connectats).
- [ ] Veure estadístiques via informació a temps real: Reserves actives, compres confirmades i recaptació parcial.
- [ ] Funcionalitat per a Crear/Editar esdeveniments, aforament o categories de preus (gestió clàssica CRUD).
- [ ] **GitHub:** `git commit -am "Dia 9: Panell d'administrador i mètriques en ple funcionament" && git push`.

## Dia 10: Testing, IA i Desplegament
- [ ] (IA - OpenSpec): Crear els documents `foundations.md`, `spec.md` i [plan.md](file:///C:/Users/usuario/.gemini/antigravity/brain/61ea2c09-517a-45c1-b18a-5235972ba55a/implementation_plan.md) per alguna de les funcions esmentades (p.e el sistema de cua).
- [ ] Deixar guardat l'historial del prompt de l'assistent a `docs/prompts-log.md`.
- [ ] Programar proves unitàries sobre l'estat, de Pinia i de les rutes. Prova Cypress per casos de concurrència extrems.
- [ ] Desplegament de l'aplicació i BD.
- [ ] **GitHub:** `git commit -am "Dia 10: Testing, IA i codi final publicat" && git push`.
