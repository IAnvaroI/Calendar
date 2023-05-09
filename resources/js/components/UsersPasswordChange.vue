<template>
    <div class="main p-3 m-0 row d-flex flex-column justify-content-center align-items-center">
        <div class="d-flex flex-column justify-content-center align-items-center bg-white shadow overflow-hidden
                    rounded col-xxl-3 col-xl-4 col-lg-5 col-md-7 col-sm-9 d-inline-block py-4">
            <h1 class="text-gray-600 mt-1 mb-3 font-bold fs-2 text-center">Зміна паролю</h1>
            <Errors :errors="errors"/>
            <form method="POST" @submit.prevent="handleSubmit">
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="oldPassword">Старий пароль</label>
                    <input id="oldPassword" type="password" v-model="form.oldPassword" required
                           class="shadow appearance-none border border-red rounded w-full py-2 px-3 text-grey-darker mb-3"/>
                </div>
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="newPassword">Новий пароль</label>
                    <input id="newPassword" type="password" v-model="form.newPassword" required
                           class="shadow appearance-none border border-red rounded w-full py-2 px-3 text-grey-darker mb-3"/>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-center">
                    <button class="bg-color-dark text-white font-bold py-2 px-4 rounded border w-75" type="submit">
                        Змінити
                    </button>
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
            oldPassword: '',
            newPassword: '',
        });

        const handleSubmit = async () => {
            try {
                let token = localStorage.getItem('JWT_TOKEN');

                const result = await axios.patch('/api/users/password', form, {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                if (result.status === 200) {
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
