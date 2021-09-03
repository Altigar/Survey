<template>
    <div class="card">
        <div class="card-body">
            <h4>{{ title }}<span v-if="isRequired" class="text-danger ms-2">*</span></h4>
            <div v-for="option in data" :key="option.id" class="form-check mb-2">
                <input v-model="value" :value="option.id" :id="`checkbox[${option.id}]`" :name="`question[${id}][]`" class="form-check-input" type="checkbox">
                <label :for="`checkbox[${option.id}]`" class="form-check-label">{{ option.text }}</label>
            </div>
            <app-form-error v-if="error">{{ error }}</app-form-error>
        </div>
    </div>
</template>

<script>
import AppFormError from "../AppFormError";

export default {
    name: "Checkbox",
    components: {AppFormError},
    props: {
        id: Number,
        isRequired: Boolean,
        type: String,
        title: String,
        optionId: Number,
        data: Array,
    },
    data() {
        return {
            value: [],
            error: null,
        };
    },
}
</script>

<style scoped>

</style>