<!-- frontend-tickets/src/components/TicketForm.vue -->
<template>
  <div class="card">
    <h2>Crear Nuevo Ticket</h2>
    
    <div v-if="successMessage" class="success">{{ successMessage }}</div>
    <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
    
    <form @submit.prevent="submitForm">
      <div class="form-group">
        <label>Título *</label>
        <input 
          type="text" 
          v-model="form.title"
          :class="{ 'error-input': errors.title }"
          placeholder="Ej: Error en el login"
        />
        <span v-if="errors.title" class="field-error">{{ errors.title }}</span>
      </div>
      
      <div class="form-group">
        <label>Descripción *</label>
        <textarea 
          v-model="form.description"
          rows="4"
          :class="{ 'error-input': errors.description }"
          placeholder="Describe el problema detalladamente..."
        ></textarea>
        <span v-if="errors.description" class="field-error">{{ errors.description }}</span>
      </div>
      
      <div class="form-group">
        <label>Prioridad *</label>
        <select v-model="form.priority" :class="{ 'error-input': errors.priority }">
          <option value="">Seleccionar prioridad</option>
          <option value="baja">Baja</option>
          <option value="media">Media</option>
          <option value="alta">Alta</option>
        </select>
        <span v-if="errors.priority" class="field-error">{{ errors.priority }}</span>
      </div>
      
      <div class="form-group">
        <label>Cliente *</label>
        <select v-model="form.client_id" :class="{ 'error-input': errors.client_id }">
          <option value="">Seleccionar cliente</option>
          <option v-for="client in clients" :key="client.id" :value="client.id">
            {{ client.name }}
          </option>
        </select>
        <span v-if="errors.client_id" class="field-error">{{ errors.client_id }}</span>
      </div>
      
      <button type="submit" :disabled="loading">
        {{ loading ? 'Creando...' : 'Crear Ticket' }}
      </button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'TicketForm',
  data() {
    return {
      form: {
        title: '',
        description: '',
        priority: '',
        client_id: ''
      },
      clients: [],
      errors: {},
      loading: false,
      successMessage: '',
      errorMessage: ''
    }
  },
  mounted() {
    this.loadClients()
  },
  methods: {
    // Cargar lista de clientes
    async loadClients() {
      try {
        const token = localStorage.getItem('token')
        const response = await fetch('http://localhost:8000/api/clients', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          this.clients = data
        }
      } catch (err) {
        console.error('Error cargando clientes:', err)
      }
    },
    
    // Validaciones en UI
    validateForm() {
      this.errors = {}
      
      if (!this.form.title.trim()) {
        this.errors.title = 'El título es obligatorio'
      } else if (this.form.title.length < 3) {
        this.errors.title = 'El título debe tener al menos 3 caracteres'
      } else if (this.form.title.length > 100) {
        this.errors.title = 'El título no puede tener más de 100 caracteres'
      }
      
      if (!this.form.description.trim()) {
        this.errors.description = 'La descripción es obligatoria'
      } else if (this.form.description.length < 10) {
        this.errors.description = 'La descripción debe tener al menos 10 caracteres'
      }
      
      if (!this.form.priority) {
        this.errors.priority = 'Debes seleccionar una prioridad'
      }
      
      if (!this.form.client_id) {
        this.errors.client_id = 'Debes seleccionar un cliente'
      }
      
      return Object.keys(this.errors).length === 0
    },
    
    async submitForm() {
      // Limpiar mensajes anteriores
      this.successMessage = ''
      this.errorMessage = ''
      
      // Validación local
      if (!this.validateForm()) {
        return
      }
      
      this.loading = true
      
      try {
        // Obtener el token del localStorage
        const token = localStorage.getItem('token')
        
        const response = await fetch('http://localhost:8000/api/tickets', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.form)
        })
        
        const data = await response.json()
        
        if (!response.ok) {
          if (response.status === 401) {
            // Token expirado o inválido
            this.errorMessage = 'Sesión expirada. Por favor inicia sesión nuevamente.'
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            setTimeout(() => {
              window.location.reload()
            }, 2000)
            return
          }
          
          if (response.status === 422 && data.errors) {
            const apiErrors = {}
            for (const [field, messages] of Object.entries(data.errors)) {
              apiErrors[field] = messages[0]
            }
            this.errors = apiErrors
            throw new Error('Por favor corrige los errores del formulario')
          }
          
          throw new Error(data.message || 'Error al crear el ticket')
        }
        
        // Éxito
        this.successMessage = '¡Ticket creado exitosamente!'
        this.form = { title: '', description: '', priority: '', client_id: '' }
        this.errors = {}
        
        this.$emit('ticket-created', data)
        
        setTimeout(() => {
          this.successMessage = ''
        }, 3000)
        
      } catch (err) {
        if (err.message !== 'Por favor corrige los errores del formulario') {
          this.errorMessage = err.message
          setTimeout(() => {
            this.errorMessage = ''
          }, 3000)
        }
      } finally {
        this.loading = false
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

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #555;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.error-input {
  border-color: #f44336 !important;
}

.field-error {
  color: #f44336;
  font-size: 12px;
  display: block;
  margin-top: 5px;
}

button {
  cursor: pointer;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  background: #42b983;
  color: white;
  font-size: 14px;
}

button:hover {
  background: #359268;
}

button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.success {
  color: green;
  padding: 10px;
  background: #dfd;
  border-radius: 4px;
  margin: 10px 0;
}

.error {
  color: red;
  padding: 10px;
  background: #fee;
  border-radius: 4px;
  margin: 10px 0;
}
</style>