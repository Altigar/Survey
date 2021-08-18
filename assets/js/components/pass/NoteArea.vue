<template>
    <div class="card">
        <div class="card-body">
            <h3>{{ title }}<span v-if="isRequired" class="text-danger ms-2">*</span></h3>
            <textarea v-model="value" :rows="rows" class="form-control resize-none mb-2"></textarea>
            <app-form-error v-if="error" class="text-danger">{{ error }}</app-form-error>
        </div>
    </div>
</template>

<script>
import AppFormError from "../AppFormError";

export default {
    name: "NoteArea",
    components: {AppFormError},
    props: {
        data: Object,
        id: Number,
        isRequired: Boolean,
        type: String,
        optionId: Number,
        title: String,
        rows: Number,
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