<?php
// app/Http/Controllers/Api/TicketController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{

    /**
     * GET /api/tickets - Listar tickets con filtros
     */
    public function index(Request $request)
    {
        try {
            $query = Ticket::with('client');

            // Filtro por prioridad
            if ($request->has('priority') && $request->priority) {
                $query->where('priority', $request->priority);
            }

            // Filtro por rango de fechas
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Filtro por texto en título (búsqueda parcial)
            if ($request->has('search') && $request->search) {
                $query->where('title', 'LIKE', '%' . $request->search . '%');
            }

            // Filtro por cliente
            if ($request->has('client_id') && $request->client_id) {
                $query->where('client_id', $request->client_id);
            }

            // Ordenar por fecha de creación (más recientes primero)
            $tickets = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $tickets,
                'total' => $tickets->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tickets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/tickets - Crear nuevo ticket
     */
    public function store(Request $request)
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'title' => 'required|string|max:120',
                'description' => 'required|string|min:10',
                'priority' => ['required', Rule::in(['baja', 'media', 'alta'])],
                'client_id' => 'required|exists:clients,id'
            ]);

            // Crear ticket
            $ticket = Ticket::create($validated);
            
            // Cargar la relación client
            $ticket->load('client');

            return response()->json([
                'success' => true,
                'message' => 'Ticket creado exitosamente',
                'data' => $ticket
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/tickets/{id} - Mostrar ticket específico
     */
    public function show($id)
    {
        try {
            $ticket = Ticket::with('client')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $ticket
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el ticket'
            ], 500);
        }
    }

    /**
     * PUT /api/tickets/{id} - Actualizar ticket
     */
    public function update(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:120',
                'description' => 'sometimes|required|string|min:10',
                'priority' => ['sometimes', 'required', Rule::in(['baja', 'media', 'alta'])],
                'client_id' => 'sometimes|required|exists:clients,id',
                'status' => ['sometimes', 'required', Rule::in(['abierto', 'en_proceso', 'cerrado'])]
            ]);

            $ticket->update($validated);
            $ticket->load('client');

            return response()->json([
                'success' => true,
                'message' => 'Ticket actualizado exitosamente',
                'data' => $ticket
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el ticket'
            ], 500);
        }
    }

    /**
     * DELETE /api/tickets/{id} - Eliminar ticket
     */
    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ticket eliminado exitosamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el ticket'
            ], 500);
        }
    }
}