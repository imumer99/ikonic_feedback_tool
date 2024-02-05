<template>
  <div class="min-h-full">
    <nav class="bg-mainDark">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <h3 class="text-white font-bold">Feedback Tool</h3>
            </div>
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <router-link to="/dashboard" class="block text-sm font-medium px-3 py-2 rounded-md"
                  :class="{ 'text-mainLight': $route.path === '/dashboard', 'text-white': $route.path !== '/dashboard' }">
                  Dashboard
                </router-link>
                <router-link to="/feedback-form" class="block text-sm font-medium px-3 py-2 rounded-md"
                  :class="{ 'text-mainLight': $route.path === '/feedback-form', 'text-white': $route.path !== '/feedback-form' }">
                  Add New Feedback
                </router-link>
              </div>
            </div>
          </div>
          <div class="hidden md:block">
            <div class="ml-4 flex items-center md:ml-6">
              <div class="relative ml-3" @click="toggleDropdown">
                <div>
                  <button type="button"
                    class="relative flex max-w-xs items-center rounded-full bg-mainDark text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-mainDark"
                    :aria-expanded="isDropdownOpen.toString()" aria-haspopup="true" id="user-menu-button">
                    <span class="absolute -inset-1.5"></span>
                    <span class="sr-only">Open user menu</span>
                    <img class="h-8 w-8 rounded-full" :src="'/assets/images/user.jpg'" alt="">
                  </button>
                </div>
                <div v-show="isDropdownOpen"
                  class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                  role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                  <a @click="logout" class="block cursor-pointer px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
                    id="user-menu-item-2">Logout</a>
                </div>
              </div>
            </div>
          </div>
          <div class="-mr-2 flex md:hidden">
            <button type="button" @click="toggleMobileMenu"
              class="relative inline-flex items-center justify-center rounded-md bg-mainDark p-2 text-mainLight hover:bg-mainLight hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-mainDark"
              aria-controls="mobile-menu" :aria-expanded="isMobileMenuOpen.toString()">
              <span class="absolute -inset-0.5"></span>
              <span class="sr-only">Open main menu</span>
              <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
              </svg>
              <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      <div v-if="isMobileMenuOpen" class="md:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
          <router-link to="/dashboard" class="block text-sm font-medium px-3 py-2 rounded-md"
            :class="{ 'text-mainLight': isMobileMenuOpen && $route.path === '/dashboard', 'text-white': !isMobileMenuOpen || $route.path !== '/dashboard' }">
            Dashboard
          </router-link>
          <router-link to="/feedback-form" class="block text-sm font-medium px-3 py-2 rounded-md"
            :class="{ 'text-mainLight': isMobileMenuOpen && $route.path === '/feedback-form', 'text-white': !isMobileMenuOpen || $route.path !== '/feedback-form' }">
            Add New Feedback
          </router-link>
        </div>
        <div class="border-t border-mainLight pb-3 pt-4">
          <div class="flex items-center px-5">
            <div class="flex-shrink-0">
              <img class="h-10 w-10 rounded-full" :src="'/assets/images/user.jpg'" alt="">
            </div>
            <div class="ml-3">
              <div class="text-sm font-medium leading-none text-white">{{ user.name }}</div>
              <div class="text-xs font-medium leading-none text-white">{{ user.email }}</div>
            </div>
          </div>
          <div class="mt-3 space-y-1 px-2">
            <a @click="logout"
              class="block rounded-md px-3 py-2 text-base font-medium text-white hover:text-white">Logout</a>
          </div>
        </div>
      </div>
    </nav>
    <main class="">
      <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useAuthStore } from '@/store/auth';

export default {
  name: 'default-layout',
  setup() {
    const authStore = useAuthStore();
    const user = authStore.authUser;
    const isMobileMenuOpen = ref(false);
    const isDropdownOpen = ref(false);

    const logout = async () => {
      await authStore.logout();
    };

    const toggleMobileMenu = () => {
      isMobileMenuOpen.value = !isMobileMenuOpen.value;
      isDropdownOpen.value = false;
    };

    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value;
      isMobileMenuOpen.value = false;
    };

    return {
      user,
      isDropdownOpen,
      isMobileMenuOpen,
      logout,
      toggleDropdown,
      toggleMobileMenu,
    };
  },
};
</script>
