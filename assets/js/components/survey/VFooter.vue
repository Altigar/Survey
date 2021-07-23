<template>
    <div class="d-flex justify-content-between border-top pt-3">
        <div>
            <button @click="$emit('save')" type="button" class="btn btn-primary">Save</button>
            <button @click="$emit('edit', $event)" type="button" class="btn btn-outline-secondary">Cancel</button>
        </div>
        <div>
            <button @click="$emit('remove')" type="button" class="btn btn-outline-danger">Remove</button>
        </div>
    </div>
</template>

<script>
export default {
    name: "VFooter"
}
</script>

<style scoped>

</style>