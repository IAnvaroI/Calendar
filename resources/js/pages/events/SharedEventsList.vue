<template>
    <div class="main p-0 m-0 row d-flex flex-column align-items-center">
        <div class="d-flex justify-content-center bg-white shadow overflow-hidden
                    rounded col-xxl-8 col-xl-10 p-4 my-4 row">
            <div class="d-flex flex-column justify-content-center col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-9 px-1">
                <EventsFilters @filter-events="handleFiltersChange" :blocking-filters="blockingFilters" :key="blockingFilters"
                               :page="currentPage" :is-shared="true" :sharing-token="$props.sharingToken"/>
            </div>
            <div class="d-flex flex-column col-xxl-9 col-xl-9 col-lg-10 px-1">
                <h1 class="text-gray-600 mt-1 mb-2 font-bold fs-2 text-center">Список подій</h1>
                <Errors :errors="errors"/>
                <div v-for="event in events" :key="event.id" class="w-full row my-1">
                    <div class="col-9 offset-1 text-center border border-dark">
                        <div class="text-center">{{ event.title }}</div>
                        <div class="row">
                            <div class="col-6 event-start-div-border"><b>Початок:</b> {{ event.start }}</div>
                            <div class="col-6 event-end-div-border"><b>Закінчення:</b> {{ event.end }}</div>
                        </div>
                        <div class="text-center">
                            <b>Теги: </b><span v-for="tag in event.tags" :key="tag.id">{{ tag.name }}&nbsp;</span>
                        </div>
                    </div>
                </div>
                <nav class="d-flex justify-content-center">
                    <ul class="pagination">
                        <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                            <a class="page-link" href="#" @click="prevPage">Попередня</a>
                        </li>
                        <li class="page-item" :class="{ 'active': page === currentPage }" v-for="page in lastPage"
                            :key="page">
                            <a class="page-link" href="#" @click="gotoPage(page)">{{ page }}</a>
                        </li>
                        <li class="page-item" :class="{ 'disabled': currentPage === lastPage }">
                            <a class="page-link" href="#" @click="nextPage">Наступна</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
import {onMounted, ref} from 'vue';
import axios from 'axios';
import EventsFilters from "../../components/events/EventsFilters.vue";
import Errors from "../../components/Errors.vue";

export default {
    props: ['sharingToken'],
    components: {
        EventsFilters,
        Errors,
    },
    setup(props) {
        const errors = ref();
        const events = ref();
        const filters = ref({
            startFrom: (new Date()).toISOString().slice(0, 10),
            period: 'month',
        });
        const currentPage = ref(1);
        const lastPage = ref();
        const blockingFilters = ref({});

        onMounted(function () {
            getEvents(currentPage.value);
        });

        const getEvents = async function (page) {
            try {
                const result = await axios.get('/api/shared/events?page=' + page, {
                    params: Object.assign({}, filters.value, {sharing_token: props.sharingToken}),
                });

                if (result.status === 200 && result.data && result.data.events.data && result.data.events.meta) {
                    events.value = result.data.events.data;
                    blockingFilters.value = result.data.blockingFilters;
                    currentPage.value = result.data.events.meta.current_page
                    lastPage.value = result.data.events.meta.last_page
                }
            } catch (exception) {
                if (exception && exception.response.data && exception.response.data.errors) {
                    errors.value = Object.values(exception.response.data.errors)
                }
            }
        }

        const handleFiltersChange = async function (firstPageEvents, paginationMeta, filtersFormData) {
            currentPage.value = paginationMeta.current_page
            lastPage.value = paginationMeta.last_page
            events.value = firstPageEvents;
            filters.value = filtersFormData;
        }

        const prevPage = function () {
            if (currentPage.value > 1) {
                getEvents(currentPage.value - 1);
            }
        };
        const nextPage = function () {
            if (currentPage.value < lastPage.value) {
                getEvents(currentPage.value + 1);
            }
        };
        const gotoPage = function (page) {
            getEvents(page);
        };

        return {
            currentPage,
            lastPage,
            events,
            errors,
            blockingFilters,
            handleFiltersChange,
            prevPage,
            nextPage,
            gotoPage,
        }
    }
}
</script>

<style scoped>
.event-start-div-border {
    border: 1px solid black;
    border-left: 0;
}

.event-end-div-border {
    border: 1px solid black;
    border-right: 0;
}
</style>
