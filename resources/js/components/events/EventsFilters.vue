<template>
    <h1 class="text-gray-600 mt-1 mb-2 font-bold fs-2 text-center">Фільтри</h1>
    <Errors :errors="errors"/>
    <form method="GET" @submit.prevent="handleSubmit">
        <div class="mb-3 d-flex flex-column">
            <label class="text-grey-darker text-center font-bold mb-2" for="events">Події</label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                    id="events" size="5" multiple v-model="form.events">
                <option v-for="event in allEvents" :key="event.id" :value="event.id">{{ event.title }}</option>
            </select>
        </div>
        <div class="mb-3 d-flex flex-column">
            <label class="text-grey-darker text-center font-bold mb-2">Теги</label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                    id="tags" size="5" multiple v-model="form.tags">
                <option v-for="tag in tags" :key="tag.id" :value="tag.id">{{ tag.name }}</option>
            </select>
        </div>
        <div class="mb-3 d-flex flex-column">
            <label class="text-grey-darker text-center font-bold mb-2" for="dates">Події починаються з дат</label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                    id="dates" size="5" multiple v-model="form.dates">
                <option v-for="date in dates" :key="date" :value="date">{{ date }}</option>
            </select>
        </div>
        <div class="mb-3 d-flex flex-column">
            <label class="text-grey-darker text-center font-bold mb-2" for="startFrom">Події починаються з дати</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                   id="startFrom" type="date" v-model="form.startFrom" @change="handleStartFromAndDateChange" required/>
        </div>
        <div class="mb-3 d-flex flex-column">
            <label class="text-grey-darker text-center font-bold mb-2" for="period">Період, за який відображатимуться
                події</label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                    id="period" v-model="form.period" @change="handleStartFromAndDateChange" required>
                <option value="day">День</option>
                <option value="week">Тиждень</option>
                <option value="month">Місяць</option>
            </select>
        </div>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <button class="bg-color-dark text-white font-bold py-2 px-4 rounded border w-75" type="submit">
                Фільтрувати
            </button>
        </div>
    </form>
</template>

<script>
import {onMounted, reactive, ref} from 'vue';
import axios from 'axios';
import Errors from "../Errors.vue";

export default {
    props: ['page', 'isShared', 'sharingToken'],
    components: {
        Errors
    },
    emits: ['filterEvents'],
    setup(props, ctx) {
        const errors = ref();
        const tags = ref();
        const dates = ref();
        const allEvents = ref();
        const form = reactive({
            events: [],
            tags: [],
            dates: [],
            startFrom: (new Date()).toISOString().slice(0, 10),
            period: 'month',
        });
        onMounted(function () {
            getTags();
            getDates();
            getAllEvents();
        });

        const getTags = async function () {
            try {
                const result = await axios.get('/api/tags');

                if (result.status === 200 && result.data && result.data.tags) {
                    tags.value = result.data.tags;
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors);
                }
            }
        }

        const getDates = async function () {
            try {
                const result = await axios.get('/api/filters/dates', {
                    params: {
                        period: form.period,
                        startFrom: form.startFrom,
                    }
                });

                if (result.status === 200 && result.data && result.data.dates) {
                    dates.value = result.data.dates;
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors);
                }
            }
        }

        const getAllEvents = async function () {
            try {
                let result;

                if (props.isShared) {
                    result = await axios.get('/api/filters/shared/events?sharing_token=' + props.sharingToken, {
                        params: {
                            period: form.period,
                            startFrom: form.startFrom,
                        }
                    });
                } else {
                    let token = localStorage.getItem('JWT_TOKEN');

                    result = await axios.get('/api/filters/auth/events', {
                        params: {
                            period: form.period,
                            startFrom: form.startFrom,
                        },
                        headers: {
                            'Authorization': 'Bearer ' + token
                        }
                    });
                }

                if (result.status === 200 && result.data && result.data.events) {
                    allEvents.value = result.data.events;
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors)
                }
            }
        }

        const handleStartFromAndDateChange = function () {
            getDates();
            getAllEvents();
        }

        const handleSubmit = async function () {
            try {
                let result;

                if (props.isShared) {
                    result = await axios.get('/api/shared/events?sharing_token=' + props.sharingToken, {
                        params: form,
                    });
                } else {
                    let token = localStorage.getItem('JWT_TOKEN');

                    result = await axios.get('/api/events?page=' + props.page, {
                        params: form,
                        headers: {
                            'Authorization': 'Bearer ' + token
                        }
                    });
                }

                if (result.status === 200 && result.data && result.data.events.data && result.data.events.meta) {
                    ctx.emit('filterEvents', result.data.events.data, result.data.events.meta, form);
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
            dates,
            allEvents,
            handleStartFromAndDateChange,
            handleSubmit,
        }
    }
}
</script>

