<template>
    <div class="d-flex flex-column justify-content-center w-full bg-white">
        <Errors :errors="errors"/>
        <div class="d-flex flex-row align-items-center justify-content-center py-2 mb-1 row">
            <router-link class="col-xxl-2 inline-block text-center hover:text-blue-darker mt-1 p-0 mx-1"
                         to="/events/create">
                <button class="bg-color-dark text-white font-bold py-2 px-4 rounded border w-100" type="submit">
                    Створити подію
                </button>
            </router-link>
            <router-link class="col-xxl-2 inline-block text-center hover:text-blue-darker mt-1 p-0 mx-1"
                         to="/user/edit">
                <button class="bg-color-dark text-white font-bold py-2 px-4 rounded border w-100" type="submit">
                    Оновити профіль
                </button>
            </router-link>
            <router-link class="col-xxl-2 inline-block text-center hover:text-blue-darker mt-1 p-0 mx-1"
                         to="/user/password-change">
                <button class="bg-color-dark text-white font-bold py-2 px-4 rounded border w-100" type="submit">
                    Змінити пароль
                </button>
            </router-link>
            <button class="col-xxl-2 bg-color-dark text-white font-bold py-2 px-4 rounded border mt-1 mx-1"
                    @click="handleUserDelete">
                Видалити профіль
            </button>
            <form method="POST" @submit.prevent="handleSubmitLogout" class="col-xxl-2 mt-1 p-0 mx-1">
                <button class="bg-color-dark text-white font-bold py-2 px-4 rounded border w-100" type="submit">
                    Вийти
                </button>
            </form>
            <button class="col-xxl-4 bg-color-dark text-white font-bold py-2 px-4 rounded border mt-1 mx-1"
                    @click="handleSharingLinkGenerate">
                Згенерувати посилання для перегляду календаря
            </button>
        </div>
    </div>
</template>

<script>
import {ref} from 'vue';
import axios from 'axios';
import {useRouter} from "vue-router";
import Errors from "./Errors.vue";

export default {
    components: {
        Errors
    },
    setup() {
        const errors = ref();
        const router = useRouter();

        const handleSubmitLogout = async function () {
            try {
                let token = localStorage.getItem('JWT_TOKEN');

                const result = await axios.post('/api/auth/logout', {}, {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                if (result.status === 200) {
                    localStorage.removeItem('JWT_TOKEN');

                    await router.push('/');
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors);
                }
            }
        }

        const handleUserDelete = async function () {
            try {
                let token = localStorage.getItem('JWT_TOKEN');

                if (confirm('Ви впевнені, що хочете видалити профіль?')) {
                    const result = await axios.delete('/api/user/delete', {
                        headers: {
                            'Authorization': 'Bearer ' + token
                        }
                    });

                    if (result.status === 200) {
                        localStorage.removeItem('JWT_TOKEN');

                        await router.push('/');
                    }
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors);
                }
            }
        }

        const handleSharingLinkGenerate = async function (event) {
            try {
                let token = localStorage.getItem('JWT_TOKEN');

                const result = await axios.get('/api/sharing-token', {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                if (result.status === 200 && result.data.token) {
                    navigator.clipboard.writeText(window.location.origin + '/shared-events/' + result.data.token)
                        .then(() => {
                            event.target.innerText = 'Посилання скопійовано в буфер обміну';
                        }, () => {
                            event.target.innerText = 'Посилання не скопіювалося в буфер обміну';
                        });

                    setTimeout(() => {
                        event.target.innerText = 'Згенерувати посилання для перегляду календаря';
                    }, 5000);
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors);
                }
            }
        }

        return {
            errors,
            handleSubmitLogout,
            handleUserDelete,
            handleSharingLinkGenerate,
        }
    }
}
</script>
