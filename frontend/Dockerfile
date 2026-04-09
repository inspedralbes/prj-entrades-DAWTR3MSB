# Etapa 1: Build
FROM node:22-alpine AS build

WORKDIR /app

# Instalar dependències
COPY package*.json ./
RUN npm install --legacy-peer-deps

# Copiar codi i generar build
COPY . .
RUN npm run build

# Etapa 2: Runtime
FROM node:22-alpine

WORKDIR /app

# Copiar només el resultat de la build
COPY --from=build /app/.output ./.output

ENV NODE_ENV=production
EXPOSE 3000

# Executar el servidor Nitro (Nuxt 3)
CMD ["node", ".output/server/index.mjs"]
