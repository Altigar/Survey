<template></template>
<script>
export default {
    name: "Base",
    data() {
        return {
            editState: false,
        }
    },
    computed: {
        edited() {
            return this.editState;
        }
    },
    methods: {
        toggleEdit(state) {
            this.editState = state;
        },
    }
}
</script>

<style scoped>

</style>