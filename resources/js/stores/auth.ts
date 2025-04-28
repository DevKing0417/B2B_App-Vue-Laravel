import { defineStore } from 'pinia';
import axios from 'axios';
import { ref } from 'vue';

interface User {
  id: number;
  name: string;
  email: string;
  employee_id: string;
  department: string | null;
  is_admin: boolean;
}

interface LoginCredentials {
  email: string;
  password: string;
}

interface RegisterData extends LoginCredentials {
  name: string;
  employee_id: string;
  department?: string;
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(localStorage.getItem('token'));
  const isAuthenticated = ref(!!token.value);

  const setToken = (newToken: string | null) => {
    token.value = newToken;
    isAuthenticated.value = !!newToken;
    if (newToken) {
      localStorage.setItem('token', newToken);
      axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
    } else {
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
    }
  };

  const setUser = (newUser: User | null) => {
    user.value = newUser;
  };

  const login = async (credentials: LoginCredentials) => {
    try {
      const response = await axios.post('/api/auth/login', credentials);
      const { token: newToken, user: newUser } = response.data;
      setToken(newToken);
      setUser(newUser);
      return true;
    } catch (error) {
      console.error('Login failed:', error);
      throw error;
    }
  };

  const register = async (data: RegisterData) => {
    try {
      const response = await axios.post('/api/auth/register', data);
      const { token: newToken, user: newUser } = response.data;
      setToken(newToken);
      setUser(newUser);
      return true;
    } catch (error) {
      console.error('Registration failed:', error);
      throw error;
    }
  };

  const logout = async () => {
    try {
      await axios.post('/api/auth/logout');
      setToken(null);
      setUser(null);
      return true;
    } catch (error) {
      console.error('Logout failed:', error);
      throw error;
    }
  };

  const fetchUser = async () => {
    try {
      const response = await axios.get('/api/auth/user');
      setUser(response.data);
      return true;
    } catch (error) {
      console.error('Failed to fetch user:', error);
      setToken(null);
      setUser(null);
      throw error;
    }
  };

  // Initialize axios headers if token exists
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser,
  };
}); 