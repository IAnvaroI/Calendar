import {createRouter, createWebHistory} from 'vue-router';
import Login from "./pages/Login.vue";
import Register from "./pages/Register.vue";
import Home from "./pages/Home.vue";
import UserEdit from "./pages/users/UserEdit.vue";
import UserPasswordChange from "./pages/users/UserPasswordChange.vue";
import CreateEvent from "./pages/events/CreateEvent.vue";
import UpdateEvent from "./pages/events/UpdateEvent.vue";
import SharedEventsList from "./pages/events/SharedEventsList.vue";

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
        },
        {
            path: '/user/edit',
            component: UserEdit,
        },
        {
            path: '/user/password-change',
            component: UserPasswordChange,
        },
        {
            path: '/events/create',
            component: CreateEvent,
        },
        {
            path: '/events/:id/edit',
            component: UpdateEvent,
            props: true
        },
        {
            path: '/shared-events/:sharingToken',
            component: SharedEventsList,
            props: true
        },
    ],
});

export default router;
