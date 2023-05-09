<template>
    <div class="main p-0 m-0 row d-flex flex-column">
        <Navbar/>
    </div>
</template>

<script>
import Navbar from "../components/Navbar.vue";
export default {
    props: ['id'],
    components: {
        Navbar
    }
}
</script>
