<template>
    <div class="main p-3 m-0 row d-flex flex-column justify-content-center align-items-center">
        <div class="d-flex flex-column justify-content-center align-items-center bg-white shadow overflow-hidden
                    rounded col-xxl-3 col-xl-4 col-lg-5 col-md-7 col-sm-9 d-inline-block py-4">
            <h1 class="text-gray-600 mt-1 mb-3 font-bold fs-2 text-center">Створити профіль</h1>
            <Errors :errors="errors"/>
            <form method="POST" @submit.prevent="handleSubmit">
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="firstname">Ім'я</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                           id="firstname" type="text" v-model="form.firstname" required/>
                </div>
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="lastname">Прізвище</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                           id="lastname" type="text" v-model="form.lastname" required/>
                </div>
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="birthDate">Дата народження</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                           id="birthDate" type="date" v-model="form.birthDate" required/>
                </div>
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="email">Електронна адреса</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                           id="email" type="text" v-model="form.email" required/>
                </div>
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="password">Пароль</label>
                    <input
                        class="shadow appearance-none border border-red rounded w-full py-2 px-3 text-grey-darker mb-3"
                        id="password" type="password" v-model="form.password" required/>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-center">
                    <button class="bg-color-dark text-white font-bold py-2 px-4 rounded border w-75" type="submit">
                        Створити
                    </button>
                    <router-link class="inline-block text-center hover:text-blue-darker" to="/">
                        Вже зареєстровані?
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import {reactive, ref} from 'vue';
import axios from 'axios';
import {useRouter} from "vue-router";
import Errors from "../components/Errors.vue";

export default {
    components: {
        Errors
    },
    setup() {
        const errors = ref();
        let router = useRouter();
        const form = reactive({
            firstname: '',
            lastname: '',
            birthDate: '',
            email: '',
            password: '',
        });

        const handleSubmit = async () => {
            try {
                const result = await axios.post('/api/auth/register', form);

                if (result.status === 200 && result.data && result.data.token) {
                    localStorage.setItem('JWT_TOKEN', result.data.token);

                    await router.push('home');
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors)
                }
            }
        }

        return {
            form,
            errors,
            handleSubmit,
        }
    }
}
</script>
