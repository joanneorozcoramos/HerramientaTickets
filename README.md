# Sistema de Gestión de Tickets

Sistema completo de gestión de tickets con backend en Laravel y frontend en Vue 3.

## 📋 Requisitos previos

- PHP >= 8.1
- Composer
- Node.js >= 16
- NPM
- SQLite 

## 🚀 Instalación

### 1. Clonar el repositorio

git clone git@github.com:joanneorozcoramos/HerramientaTickets.git
cd Gestion_Tickets_Soporte

# Backend (Laravel)
cd HerramientaTickets

# Instalar dependencias
composer install

# Copiar archivo de entorno
cp .env.example .env

# Generar key
php artisan key:generate

# Configurar base de datos (SQLite)
touch database/database.sqlite

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Instalar Laravel Sanctum (autenticación)
- php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
- php artisan migrate

# Iniciar servidor
php artisan serve

# Frontend (Vue)
cd HerramientaTickets
cd frontend-tickets

# Instalar dependencias
npm install

# Iniciar servidor de desarrollo
npm run dev


## 🔑 Credenciales de acceso al usuario de prueba del seeder

### Administrador
- **Email:** `admin@example.com`
- **Contraseña:** `password123`
- **Rol:** admin

### Usuario normal
- **Email:** `test@example.com`
- **Contraseña:** `password123`
- **Rol:** user