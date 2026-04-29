<!-- frontend-tickets/src/components/TicketFilters.vue -->
<template>
  <div class="card">
    <h3>Filtrar Tickets</h3>
    
    <div class="filters-grid">
      <div class="filter-group">
        <label>🔍 Buscar</label>
        <input 
          type="text" 
          v-model="localFilters.search"
          placeholder="Buscar por título o descripción..."
          @input="emitFilters"
        />
      </div>
      
      <div class="filter-group">
        <label>⚡ Prioridad</label>
        <select v-model="localFilters.priority" @change="emitFilters">
          <option value="">Todas</option>
          <option value="baja">Baja</option>
          <option value="media">Media</option>
          <option value="alta">Alta</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>📅 Fecha</label>
        <input 
          type="date" 
          v-model="localFilters.date"
          @change="emitFilters"
        />
      </div>
    </div>
    
    <!-- Botón para limpiar filtros -->
    <button 
      v-if="hasActiveFilters" 
      @click="clearFilters"
      class="btn-clear"
    >
      Limpiar filtros
    </button>
  </div>
</template>

<script>
export default {
  name: 'TicketFilters',
  props: {
    filters: {
      type: Object,
      default: () => ({
        priority: '',
        search: '',
        date: ''
      })
    }
  },
  data() {
    return {
      localFilters: {
        priority: this.filters.priority,
        search: this.filters.search,
        date: this.filters.date
      }
    }
  },
  computed: {
    hasActiveFilters() {
      return this.localFilters.priority || this.localFilters.search || this.localFilters.date
    }
  },
  watch: {
    filters: {
      handler(newFilters) {
        this.localFilters = { ...newFilters }
      },
      deep: true
    }
  },
  methods: {
    emitFilters() {
      this.$emit('update-filters', this.localFilters)
    },
    
    clearFilters() {
      this.localFilters = {
        priority: '',
        search: '',
        date: ''
      }
      this.emitFilters()
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

.card h3 {
  margin-bottom: 15px;
  color: #333;
}

.filters-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 15px;
  margin-bottom: 15px;
}

.filter-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #555;
  font-size: 14px;
}

.filter-group input,
.filter-group select {
  width: 100%;
}

.btn-clear {
  background: #ff9800;
  font-size: 12px;
  padding: 6px 12px;
}

.btn-clear:hover {
  background: #e68900;
}
</style>