import { createRouter, createWebHistory } from 'vue-router';
import { authService } from '../services/api';

import HomePage from '../views/HomePage.vue';
import LoginPage from '../views/LoginPage.vue';
import AdminDashboard from '../views/AdminDashboard.vue';
import OperadorDashboard from '../views/OperadorDashboard.vue';
import LojaDashboard from '../views/LojaDashboard.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage
  },
  {
    path: '/admin',
    name: 'Admin',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/operador',
    name: 'Operador',
    component: OperadorDashboard,
    meta: { requiresAuth: true, role: 'operador' }
  },
  {
    path: '/loja',
    name: 'Loja',
    component: LojaDashboard,
    meta: { requiresAuth: true, role: 'loja' }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const isAuthenticated = authService.isAuthenticated();

  if (requiresAuth && !isAuthenticated) {
    next('/login');
  } else if (to.meta.role) {
    const user = authService.getUser();
    if (user && user.role === to.meta.role) {
      next();
    } else {
      next('/login');
    }
  } else {
    next();
  }
});

export default router;
