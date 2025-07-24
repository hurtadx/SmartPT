@echo off
echo ======================================
echo    SmartPT - Setup para Evaluadores
echo ======================================
echo.

echo [1/4] Deteniendo contenedores existentes...
docker-compose down

echo.
echo [2/4] Eliminando imagenes anteriores...
docker rmi smartpt-backend smartpt-frontend 2>nul

echo.
echo [3/4] Construyendo contenedores (esto puede tomar unos minutos)...
docker-compose build

echo.
echo [4/4] Iniciando servicios...
docker-compose up -d

echo.
echo ======================================
echo            SETUP COMPLETADO
echo ======================================
echo.
echo Frontend disponible en: http://localhost:5173
echo Backend disponible en:  http://localhost:8000
echo.
echo Usuarios de prueba:
echo - admin@smartpt.com / password123
echo - user@smartpt.com / password123
echo.
echo Para ver logs: docker-compose logs -f
echo Para detener: docker-compose down
echo.
pause
