# Dockerfile per al Backend
FROM node:20-alpine

WORKDIR /app

# Instalar dependències
COPY package*.json ./
RUN npm install

# Copiar el codi font
COPY . .

# Exposar el port
EXPOSE 3001

# Executar el servidor
CMD ["node", "server.js"]
