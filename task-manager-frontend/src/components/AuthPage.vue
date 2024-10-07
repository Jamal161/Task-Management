

 

  <template>
    <div>
      <form @submit.prevent="handleSubmit">
        <h1>{{ isLogin ? 'Login' : 'Register' }}</h1>
        <input v-model="name" type="text" placeholder="Name" v-if="!isLogin" required />
        <input v-model="email" type="email" placeholder="Email" required />
        <input v-model="password" type="password" placeholder="Password" required />
        <button type="submit">{{ isLogin ? 'Login' : 'Register' }}</button>
        <button type="button" @click="toggleMode">{{ isLogin ? 'Switch to Register' : 'Switch to Login' }}</button>
        <router-link class="forgot-password" to="/forgot-password">Forgot Password?</router-link>
        <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        name: '',
        email: '',
        password: '',
        isLogin: true, 
        errorMessage: null, 
      };
    },
    methods: {
      async handleSubmit() {
        const endpoint = this.isLogin ? 'login' : 'register';
        try {
          const response = await axios.post(`http://localhost:8000/api/${endpoint}`, {
            name: this.isLogin ? undefined : this.name, 
            email: this.email,
            password: this.password,
          });
  
          if (this.isLogin) {
            localStorage.setItem('token', response.data.token); 
            localStorage.setItem('role', response.data.role ? 'admin' : 'user'); 
  
            if (response.data.role === 'admin') {
              this.$router.push('/admin/dashboard'); 
            } else {
              this.$router.push('/tasks'); 
            }
          } else {
            alert('Registration successful! Please login.');
            this.toggleMode(); 
          }
          this.errorMessage = null; 
        } catch (error) {
          this.errorMessage = error.response?.data?.message || 'An error occurred during authentication.';
        }
      },
      toggleMode() {
        this.isLogin = !this.isLogin; 
        this.name = ''; 
        this.password = ''; 
        this.email = ''; 
        this.errorMessage = null; 
      },
      logout() {
        localStorage.removeItem('token'); 
        localStorage.removeItem('role'); 
        this.$router.push('/login'); 
      },
      checkUserRole() {
        return localStorage.getItem('role'); 
      },
    },
  };
  </script>

  

<style scoped>
  
  div {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f4f7f6;
  }

  form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
  }

  h1 {
    text-align: center;
    font-family: 'Arial', sans-serif;
    margin-bottom: 20px;
    color: #333;
  }

  input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
  }

  button[type="submit"], button[type="button"] {
    width: 100%;
    padding: 10px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
    font-size: 16px;
  }

  button[type="submit"]:hover, button[type="button"]:hover {
    background-color: #218838;
  }

  router-link {
    display: block;
    margin-top: 10px;
    text-align: center;
    color: #007bff;
    text-decoration: none;
  }

  router-link:hover {
    text-decoration: underline;
  }

  
  button[type="button"] {
    background-color: #17a2b8;
  }

  button[type="button"]:hover {
    background-color: #138496;
  }

  .forgot-password {
    display: block;
    text-align: center;
    margin-top: 10px;
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
  }

  .forgot-password:hover {
    text-decoration: underline;
    color: #0056b3;
  }
</style>

  