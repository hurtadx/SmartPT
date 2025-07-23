# Usar imagen oficial de Node.js 
FROM node:20-alpine

# Configurar el directorio de trabajo
WORKDIR /app

# Copiar package.json y package-lock.json
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto de archivos
COPY . .

# Exponer puerto 5173 (puerto por defecto de Vite)
EXPOSE 5173

# Comando para iniciar la aplicación en modo desarrollo con Vite
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
