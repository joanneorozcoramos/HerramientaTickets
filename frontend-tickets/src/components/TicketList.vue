<!-- frontend-tickets/src/components/TicketList.vue -->
<template>
  <div class="card">
    <h2>Tickets <span class="count">({{ tickets.length }})</span></h2>
    
    <!-- Estado: Cargando -->
    <div v-if="loading" class="state-message loading">
      <div class="spinner"></div>
      <p>Cargando tickets...</p>
    </div>
    
    <!-- Estado: Error -->
    <div v-else-if="error" class="state-message error">
      <p>❌ {{ error }}</p>
      <button @click="$emit('retry')" class="btn-retry">Reintentar</button>
    </div>
    
    <!-- Estado: Sin resultados -->
    <div v-else-if="tickets.length === 0" class="state-message empty">
      <p>📭 No hay tickets para mostrar</p>
      <p class="hint">Crea tu primer ticket usando el formulario de arriba</p>
    </div>
    
    <!-- Lista de tickets -->
    <div v-else class="tickets-list">
      <div 
        v-for="ticket in tickets" 
        :key="ticket.id"
        class="ticket-item"
      >
        <div class="ticket-header">
          <div class="ticket-info">
            <h3 class="ticket-title">{{ ticket.title }}</h3>
            <span :class="['priority-badge', `priority-${ticket.priority}`]">
              {{ getPriorityText(ticket.priority) }}
            </span>
          </div>
          
          <button 
            @click="deleteTicket(ticket.id)"
            class="btn-delete"
            title="Eliminar ticket"
          >
            🗑️
          </button>
        </div>
        
        <p class="ticket-description">{{ ticket.description }}</p>
        
        <div class="ticket-footer">
          <span class="ticket-date">
            📅 {{ formatDate(ticket.created_at) }}
          </span>
          <span v-if="ticket.id" class="ticket-id">
            ID: #{{ ticket.id }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TicketList',
  props: {
    tickets: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    },
    error: {
      type: String,
      default: null
    }
  },
  methods: {
    getPriorityText(priority) {
      const texts = {
        baja: 'Baja',
        media: 'Media',
        alta: 'Alta'
      }
      return texts[priority] || priority
    },
    
    formatDate(dateString) {
      if (!dateString) return 'Fecha no disponible'
      const date = new Date(dateString)
      return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    
    async deleteTicket(id) {
      if (!confirm('¿Estás seguro de eliminar este ticket?')) {
        return
      }
      
      try {
        const token = localStorage.getItem('token')  // ← Obtener token
        
        const response = await fetch(`http://localhost:8000/api/tickets/${id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`  // ← Agregar token
          }
        })
        
        if (!response.ok) {
          if (response.status === 401) {
            alert('Sesión expirada. Por favor inicia sesión nuevamente.')
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            window.location.reload()
            return
          }
          const data = await response.json()
          throw new Error(data.message || 'Error al eliminar el ticket')
        }
        
        // Éxito - recargar la lista
        this.$emit('ticket-deleted', id)
        
      } catch (err) {
        alert('Error: ' + err.message)
      }
    }
  }
}
</script>

<style scoped>
.card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.card h2 {
  margin-bottom: 20px;
  color: #333;
}

.count {
  color: #999;
  font-size: 16px;
  font-weight: normal;
}

/* Estados */
.state-message {
  text-align: center;
  padding: 40px;
}

.state-message.loading {
  color: #666;
}

.state-message.error {
  color: #f44336;
  background: #fee;
  border-radius: 4px;
}

.state-message.empty {
  color: #999;
}

.hint {
  font-size: 12px;
  margin-top: 10px;
}

.spinner {
  display: inline-block;
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #42b983;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.btn-retry {
  margin-top: 10px;
  background: #2196f3;
}

.btn-retry:hover {
  background: #0b7dda;
}

/* Lista de tickets */
.tickets-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.ticket-item {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 15px;
  transition: box-shadow 0.2s;
}

.ticket-item:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.ticket-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.ticket-info {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.ticket-title {
  margin: 0;
  font-size: 18px;
  color: #333;
}

.priority-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
  color: white;
}

.priority-baja {
  background: #4caf50;
}

.priority-media {
  background: #ff9800;
}

.priority-alta {
  background: #f44336;
}

.btn-delete {
  background: transparent;
  color: #f44336;
  font-size: 18px;
  padding: 4px 8px;
}

.btn-delete:hover {
  background: #fee;
  color: #d32f2f;
}

.ticket-description {
  color: #666;
  margin: 10px 0;
  line-height: 1.5;
}

.ticket-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  color: #999;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #f0f0f0;
}

.ticket-id {
  font-family: monospace;
}
</style>