<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios (evitando duplicados)
        User::updateOrCreate(
            ["email" => "admin@example.com"],
            [
                "name" => "Admin User",
                "password" => bcrypt("password123"),
                "role" => "admin"
            ]
        );

        User::updateOrCreate(
            ["email" => "test@example.com"],
            [
                "name" => "Test User",
                "password" => bcrypt("password123"),
                "role" => "user"
            ]
        );

        // Crear clientes (evitando duplicados)
        $clients = [
            ["name" => "Empresa ABC", "email" => "contacto@abc.com", "phone" => "123456789"],
            ["name" => "Corporación XYZ", "email" => "info@xyz.com", "phone" => "987654321"],
            ["name" => "Startup 123", "email" => "hola@startup123.com", "phone" => "555555555"],
        ];

        foreach ($clients as $clientData) {
            Client::updateOrCreate(
                ["email" => $clientData["email"]],
                $clientData
            );
        }

        // Crear tickets de ejemplo (evitando duplicados)
        $tickets = [
            [
                "title" => "Error en el sistema de login",
                "description" => "Los usuarios no pueden iniciar sesión correctamente",
                "priority" => "alta",
                "client_id" => 1
            ],
            [
                "title" => "Mejora de rendimiento en reportes",
                "description" => "Los reportes tardan más de 30 segundos en cargar",
                "priority" => "media",
                "client_id" => 2
            ],
            [
                "title" => "Solicitud de nueva funcionalidad",
                "description" => "Agregar exportación a Excel en el dashboard",
                "priority" => "baja",
                "client_id" => 1
            ],
        ];

        foreach ($tickets as $ticketData) {
            Ticket::firstOrCreate($ticketData);
        }
    }
}