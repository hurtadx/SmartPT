# Usar imagen oficial de Node.js
FROM node:18-alpine

# Configurar el directorio de trabajo
WORKDIR /app

# Copiar package.json y package-lock.json
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto de archivos
COPY . .

# Exponer puerto 3000
EXPOSE 3000

# Comando para iniciar la aplicación en modo desarrollo
CMD ["npm", "start"]
