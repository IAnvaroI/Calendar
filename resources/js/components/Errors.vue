<template>
    <ul class="list-disc text-danger d-flex flex-column">
        <li v-for="value in errors" >{{ value[0] }}</li>
    </ul>
</template>

<script>
export default {
    props: {
        errors: Object
    },
}
</script>
