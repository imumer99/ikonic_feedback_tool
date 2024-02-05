<template>
  <div class="container h-screen flex items-center justify-center">
    <div class="w-full md:w-1/2 lg:w-1/3">
      <div class="card shadow-sm bg-vtd-secondary-50 p-6 rounded-lg">
        <h1 class="text-center text-xl font-bold text-mainDark mb-4">Welcome Back! Let's Dive In.</h1>
        <hr class="my-4 border-t-2 border-mainDark">
        <form @submit.prevent="login" class="grid grid-cols-1 gap-4">
          <div v-if="Object.keys(validationErrors).length > 0">
            <div class="alert alert-danger bg-danger-50 border border-danger-500 p-2 rounded">
              <ul class="mb-0">
                <li v-for="(value, key) in validationErrors" :key="key" class="text-danger-500">{{ value[0] }}</li>
              </ul>
            </div>
          </div>
          <div>
            <label for="email" class="block text-black font-semibold text-sm mb-1">Email</label>
            <input v-model="auth.email" autocomplete="off" type="text" name="email" id="email" class="w-full px-3 py-2 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight">
          </div>
          <div class="my-1">
            <label for="password" class="block text-black font-semibold text-sm mb-1">Password</label>
            <input v-model="auth.password" autocomplete="off" type="password" name="password" id="password" class="w-full px-3 py-2 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight">
          </div>
          <div class="mb-2">
            <button :disabled="processing" type="submit" class="w-full px-4 py-2 bg-mainDark text-white rounded-md font-semibold hover:bg-mainDark focus:outline-none focus:bg-mainDark">
              <span v-if="processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true">Processing...</span>
              <span v-else>Login</span>
            </button>
          </div>
          <div class="text-center">
            <label class="text-vtd-secondary-500">Don't have an account? <router-link :to="{ name: 'register' }" class="text-mainDark font-semibold">Register Now!</router-link></label>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/store/auth';

export default {
  name: "login",
  setup() {
    const authStore = useAuthStore();
    const auth = ref({
      email: "",
      password: "",
    });
    const validationErrors = ref({});
    const processing = ref(false);

    const login = async () => {
      processing.value = true;     
      await axios.get("/sanctum/csrf-cookie");
      try {
        const { data } = await axios.post("/api/login", auth.value);
        authStore.login(data.token);
      } catch ({ response }) {
        if (response.status === 422) {
          validationErrors.value = response.data.errors;
        } else {
          validationErrors.value = {};
          alert(response.data.message);
        }
      } finally {
        processing.value = false;
      }
    };

    return {
      auth,
      validationErrors,
      processing,
      login,
    };
  },
};
</script>
