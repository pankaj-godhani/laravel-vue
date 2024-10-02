import { createRouter, createWebHistory } from 'vue-router';

import Login from './components/Login.vue';
import Register from './components/Register.vue';
import Home from './components/Home.vue';
import StudentList from './components/student/StudentList.vue';
import axios from 'axios';
import StudentAdd from "./components/student/StudentAdd";
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('authToken')}`;


const checkAuth = async (to, from, next) => {
    try {
        const response = await axios.get('/api/user');
        const isAuthenticated = !!response.data;
        if (to.path !== '/login' && !isAuthenticated) {
            next('/login');
        } else {
            next();
        }
    } catch (error) {
        // User is not authenticated
        if (to.path !== '/login') {
            next('/login');
        } else {
            next();
        }
    }
};

const routes = [
    { path: '/login', component: Login },
    { path: '/', component: Home ,beforeEnter: checkAuth },
    { path: '/register', component: Register },
    { path: '/student', component: StudentList ,beforeEnter: checkAuth },
    { path: '/student/new', component: StudentAdd ,beforeEnter: checkAuth },
];

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router;
