cat > src/App.vue << 'EOF'
<template>
  <div class="container">
    <div v-if="!isAuthenticated">
      <LoginForm @login-success="onLoginSuccess" />
    </div>
    
    <div v-else>
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Sistema de Tickets</h1>
        <div>
          <span>Bienvenido, {{ user?.name }}</span>
          <button @click="logout" style="margin-left: 10px;">Cerrar Sesión</button>
        </div>
      </div>
      
      <TicketForm @ticket-created="loadTickets" />
      <TicketFilters :filters="filters" @update-filters="updateFilters" />
      <TicketList 
        :tickets="filteredTickets" 
        :loading="loading" 
        :error="error"
        @retry="loadTickets"
        @ticket-deleted="loadTickets"
      />
    </div>
  </div>
</template>

<script>
import LoginForm from './components/LoginForm.vue'
import TicketForm from './components/TicketForm.vue'
import TicketList from './components/TicketList.vue'
import TicketFilters from './components/TicketFilters.vue'

export default {
  name: 'App',
  components: {
    LoginForm,
    TicketForm,
    TicketList,
    TicketFilters
  },
  data() {
    return {
      isAuthenticated: false,
      user: null,
      tickets: [],
      loading: false,
      error: null,
      filters: {
        priority: '',
        search: '',
        date: ''
      }
    }
  },
  computed: {
    filteredTickets() {
      let result = [...this.tickets]
      if (this.filters.priority) {
        result = result.filter(t => t.priority === this.filters.priority)
      }
      if (this.filters.search) {
        const searchTerm = this.filters.search.toLowerCase()
        result = result.filter(t => t.title.toLowerCase().includes(searchTerm))
      }
      if (this.filters.date) {
        result = result.filter(t => t.created_at?.startsWith(this.filters.date))
      }
      return result
    }
  },
  mounted() {
    const token = localStorage.getItem('token')
    const user = localStorage.getItem('user')
    if (token && user) {
      this.isAuthenticated = true
      this.user = JSON.parse(user)
      this.loadTickets()
    }
  },
  methods: {
    onLoginSuccess(user) {
      this.isAuthenticated = true
      this.user = user
      this.loadTickets()
    },
    
    logout() {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      this.isAuthenticated = false
      this.user = null
      this.tickets = []
    },
    
    async loadTickets() {
      this.loading = true
      this.error = null
      const token = localStorage.getItem('token')
      
      console.log('Token:', token) // Para depurar
      
      if (!token) {
        this.error = 'No hay sesión activa'
        this.loading = false
        return
      }
      
      try {
        const response = await fetch('http://localhost:8000/api/tickets', {
          method: 'GET',  // ← Asegurar que es GET
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`  // ← Importante
          }
        })
        
        if (!response.ok) {
          if (response.status === 401) {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            this.isAuthenticated = false
            throw new Error('Sesión expirada')
          }
          throw new Error('Error al cargar tickets')
        }
        
        const data = await response.json()
        console.log('Datos recibidos:', data) // Para depurar
        
        // Como tu API devuelve { success: true, data: [...] }
        this.tickets = data.data || data  // Si viene en data.data
        
      } catch (err) {
        this.error = err.message
        console.error('Error:', err)
      } finally {
        this.loading = false
      }
    },
    
    updateFilters(newFilters) {
      this.filters = { ...this.filters, ...newFilters }
    }
  }
}
</script>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; padding: 20px; }
.error { color: red; padding: 10px; background: #fee; border-radius: 4px; margin: 10px 0; }
</style>
EOF