<template>
    <h1 class="text-gray-600 mt-1 mb-2 font-bold fs-2 text-center">Фільтри</h1>
    <Errors :errors="errors"/>
    <form method="GET" @submit.prevent="handleSubmit">
        <div class="mb-3 d-flex flex-column"
             v-if="$props.isShared && $props.blockingFilters.tags && $props.blockingFilters.tags.length">
            <p class="text-grey-darker text-center font-bold mb-2">Теги</p>
            <label v-for="tag in blockingTags" :key="tag.id">
                <input type="checkbox" :value="tag.id" v-model="form.tags">
                {{ tag.name }}
            </label>
        </div>
        <div class="mb-3 d-flex flex-column" v-else>
            <p class="text-grey-darker text-center font-bold mb-2">Теги</p>
            <label v-for="tag in tags" :key="tag.id">
                <input type="checkbox" :value="tag.id" v-model="form.tags">
                {{ tag.name }}
            </label>
        </div>
        <div class="mb-3 d-flex flex-column">
            <label class="text-grey-darker text-center font-bold mb-2" for="startFrom">Події починаються з дати</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                   id="startFrom" type="date" v-model="form.startFrom"
                   :disabled="$props.isShared && form.startFrom === $props.blockingFilters.startFrom" required/>
        </div>
        <div class="mb-3 d-flex flex-column">
            <label class="text-grey-darker text-center font-bold mb-2" for="period">Період, за який відображатимуться
                події</label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"
                    id="period" v-model="form.period"
                    :disabled="$props.isShared && form.period === $props.blockingFilters.period" required>
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
import {useFiltersStore} from "../../store/filters";

export default {
    props: ['page', 'isShared', 'sharingToken', 'blockingFilters'],
    components: {
        Errors
    },
    emits: ['filterEvents'],
    setup(props, ctx) {
        const filtersStore = useFiltersStore();
        const errors = ref();
        const tags = ref([]);
        const blockingTags = ref([]);
        const form = reactive({
            tags: [],
            startFrom: (new Date()).toISOString().slice(0, 10),
            period: 'month',
        });
        onMounted(function () {
            getTags();

            if(props.isShared) {
                form.startFrom = props.blockingFilters.startFrom ?? form.startFrom;
                form.period = props.blockingFilters.period ?? form.period;
            }
        });

        const getTags = async function () {
            try {
                const result = await axios.get('/api/tags');

                if (result.status === 200 && result.data && result.data.tags) {
                    tags.value = result.data.tags;
                    if (props.isShared) {
                        blockingTags.value = tags.value.filter(
                            tag => (props.blockingFilters.tags ?? []).includes(tag.id.toString())
                        );
                    }
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors);
                }
            }
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

                    filtersStore.$state.filters = form;

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
            blockingTags,
            handleSubmit,
        }
    }
}
</script>

