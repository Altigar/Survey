<template>
    <div class='form-check form-switch'>
        <input class='form-check-input' type='checkbox' :id="id" :name="name" :checked='value' @input="$emit('input', $event.target.checked)">
        <label class='form-check-label' :for="id"><slot></slot></label>
    </div>
</template>

<script>
export default {
    name: "AppSwitch",
    props: {
        id: String,
        name: String,
        value: Boolean,
    },
}
</script>

<style scoped>

</style>