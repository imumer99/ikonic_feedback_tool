<template>
  <div class="container mx-auto mt-5 p-4">
    <div class="max-w-md mx-auto bg-white rounded-md shadow-md overflow-hidden">
      <div class="bg-mainDark text-white p-4">
        <h1 class="text-2xl font-bold leading-tight">Your Voice Matters!</h1>
        <small>Share your feedback with the audience</small>
      </div>
      <form @submit.prevent="submitFeedback" class="p-4">
        <div class="mb-4">
          <label for="title" class="block text-black font-semibold text-sm mb-1 required_item">Title</label>
          <input v-model="feedback.title" type="text" id="title" class="w-full px-3 py-2 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight" placeholder="Enter the title">
          <span class="error" v-if="errors?.title">{{ errors.title[0] }}</span>
        </div>
        <div class="mb-4">
          <label for="category_id" class="block text-black font-semibold text-sm mb-1 required_item">Category</label>
          <select v-model="feedback.category_id" id="category_id" class="w-full px-3 py-2 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight">
            <option value="" disabled selected>-- Select --</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.title }}</option>
          </select>
          <span class="error" v-if="errors?.category_id">{{ errors.category_id[0] }}</span>
        </div>
        <div class="mb-4">
          <label for="description" class="block text-black font-semibold text-sm mb-1 required_item">Description</label>
          <textarea v-model="feedback.description" id="description" rows="4" class="w-full px-3 py-2 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight" placeholder="Enter the description"></textarea>
          <span class="error" v-if="errors?.description">{{ errors.description[0] }}</span>
        </div>
        <div class="mb-4">
          <button :disabled="loading" type="submit" class="w-full px-4 py-2 bg-mainDark text-white rounded-md font-semibold hover:bg-mainDark focus:outline-none focus:bg-mainDark">
            {{ loading ? 'Processing...' : 'Submit Feedback' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref , onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';
import {useToast} from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import axios from 'axios';

export default {
  setup() {
    const $toast = useToast();
    const authStore = useAuthStore();
    const errors = ref({});
    const categories = ref({});

    const feedback = ref({
      title: '',
      category_id: '',
      description: '',
    });

    const loading = ref(false);

    const fetchCategories = async () => {
      try {
        const headers = { Authorization: `Bearer ${authStore.authToken}` };
        const response = await axios.get('/api/category', { headers });
        categories.value = response.data;
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    };

    const submitFeedback = () => {
      loading.value = true;
      const headers = { Authorization: `Bearer ${authStore.authToken}` };
      axios.post('/api/feedback', feedback.value, {headers})
        .then(response => {
          $toast.success('Feedback submitted successfully!');
          resetForm();
        })
        .catch(error => {
          if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
            $toast.error('Failed to submit feedback.');
          }
        })
        .finally(() => {
          loading.value = false;
        });
    };

    const resetForm = () => {
      feedback.value = {
        title: '',
        category_id: '',
        description: '',
      };
    };

    onMounted(() => {
      fetchCategories();
    });

    return {
      feedback,
      errors,
      categories,
      loading,
      submitFeedback,
    };
  },
};
</script>
