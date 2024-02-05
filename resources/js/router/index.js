import { createWebHistory, createRouter } from 'vue-router';
import { useAuthStore } from '@/store/auth';
import Login from '@/components/Login.vue';
import Register from '@/components/Register.vue';
import DahboardLayout from '@/components/layouts/Default.vue';
import Dashboard from '@/components/FeedbackList.vue';
import FeedbackForm from '@/components/FeedbackForm.vue';

const routes = [
    {
        name: "login",
        path: "/login",
        component: Login,
        meta: {
            middleware: "guest",
            title: `Login`
        }
    },
    {
        name: "register",
        path: "/register",
        component: Register,
        meta: {
            middleware: "guest",
            title: `Register`
        }
    },
    {
        path: "/",
        component: DahboardLayout,
        meta: {
            middleware: "auth"
        },
        children: [
            {
                name: "dashboard",
                path: '/dashboard',
                component: Dashboard,
                meta: {
                    title: `Feedback Tool | Dashboard`
                }
            },
            {
                name: "feedback.form",
                path: '/feedback-form',
                component: FeedbackForm,
                meta: {
                    title: `Feedback Tool | Feedback Form`
                }
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    document.title = to.meta.title;

    const store = useAuthStore();
    if (to.meta.middleware === 'guest') {
        if (store.isAuthenticated) {
          next({ name: 'dashboard' });
        } else {
          next();
        }
      } else {
        if (store.isAuthenticated) {
          next();
        } else {
          next({ name: 'login' });
        }
    }
    
});

export default router;
