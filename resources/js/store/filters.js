import {defineStore} from 'pinia';
export const useFiltersStore = defineStore('filters', {
    state: () => ({
        filters: {},
    }),
});
