import { defineStore } from 'pinia';
import axios from 'axios';
import router from '@/router';

export const useAuthStore = defineStore({
  id: 'auth',
  state: () => ({
    authenticated: false,
    user: null,
    token: null,
  }),
  getters: {
    isAuthenticated(state) {
      return state.authenticated;
    },
    authUser(state) {
      return state.user;
    },
    authToken(state) {
      return state.token;
    },
  },
  actions: {
    async login(token) {
      try {
        this.token = token;
        const headers = { Authorization: `Bearer ${this.token}` };
        const { data } = await axios.get("/api/user", { headers });
        this.user = data;
        this.authenticated = true
        router.push({ name: 'dashboard' });
      } catch (error) {
        this.user = null;
        this.authenticated = false;
        this.token = null;
      }
    },
    async logout() {
      const headers = { Authorization: `Bearer ${this.token}` };
      const { data } = await axios.get('/api/logout', { headers });
      localStorage.removeItem('auth');
      this.authenticated = false;
      router.push({ name: 'login' });
    },
  },
  persist: true
});
