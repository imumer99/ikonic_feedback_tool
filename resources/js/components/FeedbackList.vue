<template>
    <div class="container mt-5">
        <h2 class="text-2xl font-bold mb-4">Insights from Audience Feedback</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="feedbackItem in feedbackItems" :key="feedbackItem.id" class="py-4">
                <div class="bg-white shadow-md rounded-md overflow-hidden">
                    <div class="h-30 bg-mainDark">
                        <div class="flex items-center px-5 py-2">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" :src="'/assets/images/user.jpg'" alt="">
                            </div>
                            <div class="ml-3">
                                <div class="text-sm leading-none text-white">{{ feedbackItem.user.name }}</div>
                                <div class="text-xs leading-none text-white">{{ feedbackItem.user.email }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pt-2 pb-3">
                        <h5 v-if="feedbackItem.title.length > 30" class="text-sm font-bold mb-2">
                            {{ feedbackItem.title.substring(0, 30) }}...
                        </h5>
                        <h5 v-else class="text-sm font-bold mb-2">
                            {{ feedbackItem.title }}
                        </h5>
                        <span class="text-info-500 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-info-500">
                            {{ feedbackItem.category.title }}
                        </span>
                        <button @click="openModal(feedbackItem)"
                            class="btn btn-sm text-xs float-right mb-2 bg-danger-600 text-white px-2 py-1 rounded">Leave
                            Comment</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center mt-4">
          <TailwindPagination
            :data="feedbackItemsPagination"
            @pagination-change-page="fetchFeedbackItems"
        />
    </div>
        
        <CommentModal :selectedFeedbackItem="selectedFeedbackItem" :commentContent="commentContent"
            @update:commentContent="updateCommentContent" :openModal="openModal" :closeModal="closeModal"
            :submitComment="submitComment" :applyFormatting="applyFormatting" :mentionUsers="mentionUsers" />
    </div>
</template>
  
<script>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';
import axios from 'axios';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import CommentModal from './CommentModal.vue';
import { TailwindPagination } from 'laravel-vue-pagination';

export default {
    components: {
        CommentModal,
        TailwindPagination
    },
    setup() {
        const $toast = useToast();
        const authStore = useAuthStore();
        const feedbackItems = ref([]);
        const feedbackItemsPagination = ref([]);
        const selectedFeedbackItem = ref(null);
        const commentContent = ref('');

        const fetchFeedbackItems = async (page = 1) => {
            try {
                const headers = { Authorization: `Bearer ${authStore.authToken}` };
                const response = await axios.get(`/api/feedback?page=${page}`, { headers });
                feedbackItems.value = response.data.data;
                feedbackItemsPagination.value = response.data;

            } catch (error) {
                console.error('Error fetching feedback items:', error);
            }
        };

        const openModal = (feedbackItem) => {
            selectedFeedbackItem.value = feedbackItem;
            this.showMentionDropdown = false;
        };

        const closeModal = () => {
            selectedFeedbackItem.value = null;
            commentContent.value = '';
        };

        const submitComment = async () => {
            try {
                const headers = { Authorization: `Bearer ${authStore.authToken}` };
                const comment = {
                    content: commentContent.value,
                };
                await axios.post(`/api/feedback/${selectedFeedbackItem.value.id}/comments`, comment, { headers });
                $toast.success('Comment added successfully!');
                fetchFeedbackItems();
                commentContent.value = '';
                closeModal();
            } catch (error) {
                console.error('Error submitting comment:', error);
            }
        };

        const applyFormatting = (format) => {
            const textarea = document.getElementById('commentContent');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);
            switch (format) {
                case 'bold':
                    insertFormattedText('**', '**', selectedText);
                    break;
                case 'italic':
                    insertFormattedText('*', '*', selectedText);
                    break;
                case 'code':
                    insertFormattedText('`', '`', selectedText);
                    break;
            }
        };

        const insertFormattedText = (startTag, endTag, selectedText) => {
            const textarea = document.getElementById('commentContent');
            const cursorPosition = textarea.selectionStart;
            const currentText = commentContent.value;

            const newText =
                currentText.substring(0, cursorPosition) +
                startTag + selectedText + endTag +
                currentText.substring(textarea.selectionEnd);

            commentContent.value = newText;
        };

        const mentionUsers = async (feedbackId) => {
            try {
                const headers = { Authorization: `Bearer ${authStore.authToken}` };
                const response = await axios.get(`/api/feedback/${feedbackId}/linked-users`, { headers });
                const linkedUsers = response.data.user_names;
                return linkedUsers;
            } catch (error) {
                console.error('Error fetching linked users:', error);
                return [];
            }
        };

        const updateCommentContent = (newContent) => {
            commentContent.value = newContent;
        };

        onMounted(() => {
            fetchFeedbackItems();
        });

        return {
            feedbackItems,
            feedbackItemsPagination,
            selectedFeedbackItem,
            commentContent,
            openModal,
            closeModal,
            submitComment,
            applyFormatting,
            updateCommentContent,
            mentionUsers,
            fetchFeedbackItems
        };
    },
};
</script>  