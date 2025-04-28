import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { guest: true },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: { guest: true },
    },
    {
      path: '/campaigns',
      name: 'campaigns',
      component: () => import('@/views/campaigns/CampaignListView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/campaigns/create',
      name: 'campaign-create',
      component: () => import('@/views/campaigns/CampaignCreateView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/campaigns/:id',
      name: 'campaign-detail',
      component: () => import('@/views/campaigns/CampaignDetailView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/my-donations',
      name: 'my-donations',
      component: () => import('@/views/donations/MyDonationsView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('@/views/profile/ProfileView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('@/views/admin/AdminDashboardView.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin);
  const isGuest = to.matched.some(record => record.meta.guest);

  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchUser();
    } catch (error) {
      authStore.logout();
      return next('/login');
    }
  }

  if (requiresAuth && !authStore.isAuthenticated) {
    return next('/login');
  }

  if (requiresAdmin && !authStore.user?.is_admin) {
    return next('/');
  }

  if (isGuest && authStore.isAuthenticated) {
    return next('/');
  }

  next();
});

export default router; 