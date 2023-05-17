<template>
    <div class="main p-0 m-0 row d-flex flex-column align-items-center">
        <Navbar/>
        <EventsList/>
    </div>
</template>

<script>
import Navbar from "../components/Navbar.vue";
import EventsList from "../components/events/EventsList.vue";

export default {
    components: {
        EventsList,
        Navbar
    }
}
</script>
