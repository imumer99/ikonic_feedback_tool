<template>
  <div v-if="selectedFeedbackItem" class="fixed z-1000 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-5"></div>
      </div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <div
        class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6 overflow-scroll-y"
        role="dialog" aria-modal="true" aria-labelledby="modal-headline">
        <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
          <button @click="closeModal" type="button" data-behavior="cancel"
            class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
          {{ selectedFeedbackItem.title }}
        </h3>
        <div class="mt-3 w-full">
          <div class="mt-4">
            <form @submit.prevent="submitComment">
              <div class="mb-4 relative">
                <label for="commentContent" class="text-sm font-medium text-gray-700">Your Comment:</label>
                <textarea :value="commentContent" @input="handleCommentInput" id="commentContent" rows="2"
                  class="mt-1 block w-full p-3 rounded-md border border-mainLight text-sm focus:outline-none focus:border-mainLight"></textarea>
                <div v-if="showMentionDropdown"
                  class="absolute z-10 w-48 top-1/3 mt-1 ml-3 max-h-40 overflow-y-auto comment-container">
                  <ul
                    class="bg-white border border-gray-300 rounded-md shadow-md overflow-hidden divide-y divide-gray-200">
                    <li v-for="mention in mentionUsersList" :key="mention.id" @click="selectMention(mention)"
                      class="cursor-pointer p-2 hover:bg-gray-100 flex items-center space-x-2">
                      <img :src="'/assets/images/user.jpg'" class="h-8 w-8 rounded-full" alt="">
                      <span class="text-mainDark text-sm">{{ mention.name }}</span>
                    </li>
                  </ul>
                </div>
                <div class="mt-2 flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <strong class="text-white rounded px-2 font-semibold cursor-pointer bg-black-500"
                      @click="applyFormatting('bold')">B</strong>
                    <em class="text-white rounded px-2 font-semibold cursor-pointer bg-black-500"
                      @click="applyFormatting('italic')">I</em>
                    <code class="text-white rounded px-2 font-semibold cursor-pointer bg-black-500"
                      @click="applyFormatting('code')">Code</code>
                  </div>
                  <div class="flex items-center space-x-2">
                    <button @click="closeModal" type="button" data-behavior="cancel"
                      class="rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                      Cancel
                    </button>
                    <button type="submit"
                      class="rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                      Add Comment
                    </button>
                  </div>
                </div>
                <hr class="mt-5">
              </div>
            </form>
          </div>
        </div>
        <div v-if="selectedFeedbackItem.comments.length > 0" class="mt-5 max-h-40 overflow-y-auto comment-container">
          <h4 class="text-sm font-medium mb-4">Comments ({{ selectedFeedbackItem.comments.length }})</h4>
          <div v-for="comment in selectedFeedbackItem.comments.slice().reverse()" :key="comment.id" class="mb-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <img class="h-10 w-10 rounded-full" :src="'/assets/images/user.jpg'" alt="">
              </div>
              <div class="ml-3">
                <div class="text-xs leading-none text-gray-500">
                  <span class="text-mainLight font-semibold">{{ comment.user.name }}</span> -
                  {{ formatDate(comment.created_at) }}
                </div>
                <div class="text-sm leading-none text-black-500 mt-1" v-html="formatCommentMessage(comment.message)">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
export default {
  props: {
    selectedFeedbackItem: Object,
    commentContent: String,
    openModal: Function,
    closeModal: Function,
    submitComment: Function,
    applyFormatting: Function,
    mentionUsers: Function,
  },
  data() {
    return {
      mentionUsersList: [],
      showMentionDropdown: false,
    };
  },
  methods: {

    formatDate(date) {
      return moment(date).format('MMMM D, YYYY [at] h:mm A');
    },

    formatCommentMessage(message) {
      const formattedMessage = message
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/`(.*?)`/g, '<code>$1</code>')
        .replace(/@(\w+)/g, '<span class="mention">@$1</span>');
      return formattedMessage;
    },

    async handleCommentInput(event) {
      this.$emit('update:commentContent', event.target.value);

      const words = event.target.value.split(/\s+/);
      const lastWord = words[words.length - 1];
      if (lastWord.startsWith('@')) {
        const feedbackId = this.selectedFeedbackItem.id;
        this.mentionUsersList = await this.mentionUsers(feedbackId);
        this.showMentionDropdown = true;
      } else {
        this.showMentionDropdown = false;
      }
    },

    selectMention(mention) {
      const textarea = document.getElementById('commentContent');
      const cursorPosition = textarea.selectionStart;
      const currentText = this.commentContent;
      this.$emit('mentionSelected', mention);
      const newText =
        currentText.substring(0, cursorPosition) +
        `${mention.name} ` +
        currentText.substring(textarea.selectionEnd);
      this.$emit('update:commentContent', newText);
      this.$nextTick(() => {
        textarea.focus();
      });
      this.mentionUsersList = [];
      this.showMentionDropdown = false;
    },
  },
};
</script>

<style scoped>
ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

li {
  display: flex;
  align-items: center;
}

img {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  margin-right: 0px;
}
</style>
