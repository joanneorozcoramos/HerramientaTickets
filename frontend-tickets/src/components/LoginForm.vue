cat > src/components/LoginForm.vue << 'EOF'
<template>
  <div class="card">
    <h2>Iniciar Sesión</h2>
    
    <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
    
    <form @submit.prevent="handleLogin">
      <div class="form-group">
        <label>Email</label>
        <input type="email" v-model="credentials.email" required />
      </div>
      
      <div class="form-group">
        <label>Contraseña</label>
        <input type="password" v-model="credentials.password" required />
      </div>
      
      <button type="submit" :disabled="loading">
        {{ loading ? 'Iniciando...' : 'Iniciar Sesión' }}
      </button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'LoginForm',
  data() {
    return {
      credentials: {
        email: 'admin@example.com',
        password: 'password123'
      },
      loading: false,
      errorMessage: ''
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true
      this.errorMessage = ''
      
      try {
        const response = await fetch('http://localhost:8000/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(this.credentials)
        })
        
        const data = await response.json()
        
        if (!response.ok) {
          throw new Error(data.message || 'Error de login')
        }
        
        // Guardar token y usuario
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))
        
        // Emitir evento de login exitoso
        this.$emit('login-success', data.user)
        
      } catch (err) {
        this.errorMessage = err.message
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.card {
  max-width: 400px;
  margin: 50px auto;
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.form-group {
  margin-bottom: 15px;
}
.form-group label {
  display: block;
  margin-bottom: 5px;
}
.form-group input {
  width: 100%;
  padding: 8px;
}
button {
  width: 100%;
}
</style>
EOF