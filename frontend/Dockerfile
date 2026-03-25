# Dockerfile per al Frontend (Mode Desenvolupament)
FROM node:22-alpine

WORKDIR /app

# Instalar dependències
COPY package*.json ./
RUN npm install --legacy-peer-deps

# Copiar el codi font
COPY . .

# Exposar el port de Nuxt
EXPOSE 3000

# Executar en mode dev
CMD ["npm", "run", "dev", "--", "--host"]
