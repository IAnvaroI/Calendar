<template>
        <div class="d-flex flex-column justify-content-center w-full bg-white">
            <ul class="list-disc text-danger" v-for="(value) in errors">
                <li>{{ value[0] }}</li>
            </ul>
            <div class="d-flex flex-row align-items-center justify-content-between py-2 mb-1">
                <form method="POST" @submit.prevent="handleSubmitLogout">
                    <button class="bg-color-dark text-white font-bold py-2 px-4 rounded border" type="submit">
                        Вийти
                    </button>
                </form>
            </div>
        </div>
</template>

<script>
import {ref} from 'vue';
import axios from 'axios';
import {useRouter} from "vue-router";

export default {
    setup() {
        const errors = ref();
        const router = useRouter();

        const handleSubmitLogout = async () => {
            try {
                let token = localStorage.getItem('JWT_TOKEN');

                const result = await axios.post('/api/auth/logout', {},{
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

        return {
            errors,
            handleSubmitLogout,
        }
    }
}
</script>
