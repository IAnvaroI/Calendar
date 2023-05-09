<template>
    <ul class="list-disc text-danger text-center" v-for="(value) in errors">
        <li>{{ value[0] }}</li>
    </ul>
</template>

<script>
export default {
    props: {
        errors: Object
    },
}
</script>
