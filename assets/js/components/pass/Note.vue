<template>
    <div class="card">
        <div class="card-body">
            <h3>{{ title }}<span v-if="isRequired" class="text-danger ms-2">*</span></h3>
            <input :key="id" v-model="value" class="form-control mb-2" type="text">
            <app-form-error v-if="error">{{ error }}</app-form-error>
        </div>
    </div>
</template>

<script>
import AppFormError from "../AppFormError";

export default {
    name: "Note",
    components: {AppFormError},
    props: {
        id: Number,
        isRequired: Boolean,
        type: String,
        optionId: Number,
        title: String,
    },
    data() {
        return {
            value: null,
            error: null,
        };
    },
}
</script>

<style scoped>

</style>