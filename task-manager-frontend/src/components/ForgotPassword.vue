<template>
    <div>
      
      <form @submit.prevent="handleForgotPassword">

        <h1>Forgot Password</h1>

        
        <input v-model="email" type="email" placeholder="Email" required />
        <button type="submit">Send Password Reset Link</button>
        <router-link class="login" to="/">Login</router-link>

      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        email: ''
      };
    },
    methods: {
      async handleForgotPassword() {
        try {
          await axios.post('http://localhost:8000/api/forgot-password', {
            email: this.email,
          });
          alert('Password reset link sent! Check your email.');
          this.email = ''; 
        } catch (error) {
          console.error('Error sending password reset link:', error.response.data);
        }
      }
    }
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

  input[type="email"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
  }

  button[type="submit"] {
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

  button[type="submit"]:hover {
    background-color: #218838;
  }

  .login {
    display: block;
    text-align: center;
    margin-top: 10px;
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
  }

  .login:hover {
    text-decoration: underline;
    color: #0056b3;
  }
</style>
