<template>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <slot></slot>
        <button @click="close" type="button" class="btn-close" aria-label="Close"></button>
    </div>
</template>

<script>
export default {
    name: "AppError",
    methods: {
        close() {
            this.$emit('close');
        }
    },
}
</script>

<style scoped>

</style>