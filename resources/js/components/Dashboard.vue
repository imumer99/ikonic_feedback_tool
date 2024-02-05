<template>
  <div class="container">
  </div>
</template>

<script>
import { useAuthStore } from '@/store/auth';

export default {
  name: 'dashboard',
  setup() {
    const authStore = useAuthStore();
    const user = authStore.authUser.data;
    return {
      user,
    };
  },
};
</script>
