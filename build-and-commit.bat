@echo off
echo ========================================
echo   SGQ OTI - Build and Deploy Script
echo ========================================
echo.

echo [1/4] Instalando dependencias...
call npm install

echo.
echo [2/4] Executando build de producao...
call npm run build

echo.
echo [3/4] Copiando .gitignore para producao...
copy ".gitignore.temp" ".gitignore"

echo.
echo [4/4] Instrucoes para commit:
echo.
echo Para commitar os arquivos de producao e backend:
echo   git add dist/
echo   git add backend/
echo   git add .gitignore
echo   git commit -m "Deploy: Build de producao e backend atualizados"
echo   git push origin main
echo.
echo ========================================
echo   Build concluido com sucesso!
echo ========================================
pause
