import {createRouter, createWebHistory} from 'vue-router';
import Login from "./pages/Login.vue";
import Register from "./pages/Register.vue";
import Home from "./pages/Home.vue";
import UsersEdit from "./components/UsersEdit.vue";
import UsersPasswordChange from "./components/UsersPasswordChange.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: Login,
        },
        {
            path: '/register',
            component: Register,
        },
        {
            path: '/home',
            component: Home,
            props: true
        },
        {
            path: '/users/edit',
            component: UsersEdit,
            props: true
        },
        {
            path: '/users/password-change',
            component: UsersPasswordChange,
            props: true
        },
    ],
});

router.beforeEach((to, from, next) => {
    if (to.path !== '/' && to.path !== '/register' && !isAuthenticated()) {
        return next({path: '/'})
    }

    return next();
});

function isAuthenticated() {
    return Boolean(localStorage.getItem('JWT_TOKEN'));
}

export default router;
