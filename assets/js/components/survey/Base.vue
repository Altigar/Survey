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
        edited: {
            get() {
                return this.editState;
            },
            set(state) {
                this.editState = state;
            }
        }
    },
}
</script>

<style scoped>

</style>