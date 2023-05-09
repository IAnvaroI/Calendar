import {createRouter, createWebHistory} from 'vue-router';
import Login from "./pages/Login.vue";
import Register from "./pages/Register.vue";
import Home from "./pages/Home.vue";
import UserEdit from "./components/UserEdit.vue";
import UserPasswordChange from "./components/UserPasswordChange.vue";

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
            name: 'home',
            component: Home,
            props: true
        },
        {
            path: '/user/edit',
            component: UserEdit,
            props: true
        },
        {
            path: '/user/password-change',
            component: UserPasswordChange,
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
