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


## 📝 Prompts utilizados para generar el proyecto

A continuación se listan los prompts principales que se utilizaron para construir este sistema con la ayuda de IA:

### 1. Frontend Vue
- vamos hacer este front en vue

- Frontend (Vue) - Formulario para crear ticket. - Listado con filtros (prioridad, fecha, búsqueda por texto). - Mostrar estados loading, errores y éxito. - Validaciones básicas en UI y manejo de errores de API.

- vamos hacerlo lo mas basico posible sin cosas adicionales

### 2. Backend Laravel
- Backend (Laravel)
- 1.1. Crear endpoint POST /api/tickets para registrar ticket con: - titulo (requerido, máx 120) - descripcion (requerido) - prioridad (baja, media, alta) - cliente_id (debe existir)
- 1.2. Crear endpoint GET /api/tickets con filtros por: - prioridad - rango de fechas - texto en título (búsqueda parcial) - Implementar validaciones, manejo de errores y respuesta JSON consistente. - Proteger rutas con autenticación.

### 3. Migración para campo role
- me falta generar este archivo
- // database/migrations/xxxx_add_role_to_users_table.php

### 4. Seeder para datos de prueba
- me falta este
- // database/seeders/DatabaseSeeder.php

### 5. Corrección de errores comunes ``` - Error: Class "App\Http\Controllers\Controller" not found - Solución: Crear la clase Controller base abstracta - Error: $this->middleware() no funciona en Laravel 11 - Solución: Mover la autenticación a las rutas - Error: 422 Unprocessable Content al crear ticket - Solución: Agregar campo client_id al formulario ``` 

### 6. Funcionalidad DELETE ``` nos falta el delete Request URL: http://localhost:8000/api/tickets/4 Request Method: DELETE Status Code: 401 Unauthorized Solución: Agregar el token al header Authorization en la petición DELETE ``` 


