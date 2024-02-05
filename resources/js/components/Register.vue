<template>
  <div class="container h-screen flex items-center justify-center">
    <div class="w-full md:w-1/2 lg:w-1/3">
      <div class="card shadow-sm bg-vtd-secondary-50 p-6 rounded-lg">
        <h1 class="text-center text-xl font-bold text-mainDark mb-4">Unlock Your Journey with Us!</h1>
        <hr class="my-4 border-t-2 border-mainDark">
        <form @submit.prevent="register" class="grid grid-cols-1 gap-4">
          <div v-if="Object.keys(validationErrors).length > 0">
            <div class="alert alert-danger bg-danger-50 border border-danger-500 p-2 rounded">
              <ul class="mb-0">
                <li v-for="(value, key) in validationErrors" :key="key" class="text-danger-500">{{ value[0] }}</li>
              </ul>
            </div>
          </div>
          <div>
            <label for="name" class="block text-black font-semibold text-sm mb-1">Name</label>
            <input v-model="user.name" autocomplete="off" type="text" name="name" id="name" placeholder="Enter name" class="w-full px-3 py-2 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight">
          </div>
          <div class="my-1">
            <label for="email" class="block text-black font-semibold text-sm mb-1">Email</label>
            <input v-model="user.email" autocomplete="off" type="text" name="email" id="email" placeholder="Enter Email" class="w-full px-3 py-2 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight">
          </div>
          <div>
            <label for="password" class="block text-black font-semibold text-sm mb-1">Password</label>
            <input v-model="user.password" autocomplete="off" type="password" name="password" id="password" placeholder="Enter Password" class="w-full px-3 py-2 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight">
          </div>
          <div class="my-1">
            <label for="password_confirmation" class="block text-black font-semibold text-sm mb-1">Confirm Password</label>
            <input v-model="user.password_confirmation" autocomplete="off" type="password" name="password_confirmation" id="password_confirmation" placeholder="Enter Password" class="w-full px-3 py-2 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight">
          </div>
          <div class="mb-1">
            <button :disabled="processing" type="submit" class="w-full px-4 py-2 bg-mainDark text-white rounded-md font-semibold hover:bg-mainDark focus:outline-none focus:bg-mainDark">
              <span v-if="processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true">Processing...</span>
              <span v-else>Register</span>
            </button>
          </div>
          <div class="text-center">
            <label class="text-vtd-secondary-500">Already have an account? <router-link :to="{ name: 'login' }" class="text-mainDark font-semibold">Login Now!</router-link></label>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { ref } from 'vue';
import { useAuthStore } from '@/store/auth';

export default {
  name: 'register',
  setup() {
    const authStore = useAuthStore();
    const user = ref({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    });
    const validationErrors = ref({});
    const processing = ref(false);

    const register = async () => {
      processing.value = true;
      await axios.get('/sanctum/csrf-cookie');
      try {
        const { data } = await axios.post('/api/register', user.value);
        validationErrors.value = {};
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
      user,
      validationErrors,
      processing,
      register,
    };
  },
};
</script>
