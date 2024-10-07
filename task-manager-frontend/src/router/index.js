

import axios from 'axios';
import Vue from 'vue';
import Router from 'vue-router';
import VueRouter from 'vue-router';
import AuthPage from '../components/AuthPage.vue';
import TaskPage from '../components/TaskPage.vue';
import ForgotPassword from '../components/ForgotPassword.vue';
import AdminDashboard from '../components/AdminDashboard.vue'; 

Vue.use(Router);

Vue.use(VueRouter);

axios.defaults.baseURL = '/api';
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}


const routes = [
  { path: '/', component: AuthPage },
  { path: '/admin/dashboard', component: AdminDashboard, meta: { requiresAdmin: true } }, 
  { path: '/tasks', component: TaskPage},
  
  { path: '/forgot-password', component: ForgotPassword }
];

const router = new Router({
  routes
});


router.beforeEach((to, from, next) => {
  const role = localStorage.getItem('role');

  console.log('Navigating to admin-dashboard. User role:', role);
  if (role === 'admin') {
    next(); 
  } else {
    console.log('Redirecting to tasks due to insufficient permissions.');
    next('/tasks'); 
  }
});

export default router;
