<template>
    <div class="main p-3 m-0 row d-flex flex-column justify-content-center align-items-center">
        <div class="d-flex flex-column justify-content-center align-items-center bg-white shadow overflow-hidden
                    rounded col-xxl-3 col-xl-4 col-lg-5 col-md-7 col-sm-9 d-inline-block py-4">
            <h1 class="text-gray-600 mt-1 mb-3 font-bold fs-2 text-center">Змінити подію</h1>
            <Errors :errors="errors"/>
            <form method="POST" @submit.prevent="handleSubmit">
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="title">Назва</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                           id="title" type="text" v-model="form.title" required/>
                </div>
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="start">Початок події</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                           id="start" type="datetime-local" v-model="form.start" required/>
                </div>
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="end">Завершення події</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                           id="end" type="datetime-local" v-model="form.end" required/>
                </div>
                <div class="mb-3 d-flex flex-column">
                    <label class="text-grey-darker text-center font-bold mb-2" for="tags">Теги</label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                            id="tags" size="5" multiple v-model="form.tags">
                        <option v-for="tag in tags" :key="tag.id" :value="tag.id">{{ tag.name }}</option>
                        >
                    </select>
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
import {onMounted, reactive, ref} from 'vue';
import axios from 'axios';
import {useRouter} from "vue-router";
import Errors from "../../components/Errors.vue";

export default {
    props: ['id'],
    components: {
        Errors
    },
    setup(props) {
        const errors = ref();
        const tags = ref();
        let router = useRouter();
        const form = reactive({
            title: '',
            start: '',
            end: '',
            tags: [],
        });
        onMounted(function () {
            getTags();
            preFillingForm();
        });

        const getTags = async function () {
            try {
                const result = await axios.get('/api/tags');

                if (result.status === 200 && result.data && result.data.tags) {
                    tags.value = result.data.tags
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors);
                }
            }
        }

        const preFillingForm = async function () {
            try {
                let token = localStorage.getItem('JWT_TOKEN');

                const result = await axios.get('/api/events/' + props.id + '/edit', {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });
                if (result.status === 200 && result.data && result.data.event) {
                    let event = result.data.event;

                    for (const key in event) {
                        form[key] = event[key];
                    }

                    form['tags'] = event['tags'].map(function (tag) {
                        return tag.id;
                    })
                }
            } catch (exception) {
                if (exception && exception.response && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors);
                }
            }
        }

        const handleSubmit = async function () {
            try {
                let token = localStorage.getItem('JWT_TOKEN');

                const result = await axios.patch('/api/events/' + props.id, form, {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                if (result.status === 200) {
                    await router.push({name: 'home'});
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors);
                }
            }
        }

        return {
            form,
            errors,
            tags,
            handleSubmit,
        }
    }
}
</script>
