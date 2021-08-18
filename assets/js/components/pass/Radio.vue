<template>
    <div class="card">
        <div class="card-body">
            <h3>{{ title }}<span v-if="isRequired" class="text-danger ms-2">*</span></h3>
            <div v-for="option in data" :key="option.id" class="form-check mb-2">
                <input v-model="value" :value="option.id" :id="`radio[${option.id}]`" :name="`radio[${id}]`" class="form-check-input" type="radio">
                <label :for="`radio[${option.id}]`" class="form-check-label">{{ option.text }}</label>
            </div>
            <app-form-error v-if="error">{{ error }}</app-form-error>
        </div>
    </div>
</template>

<script>
import AppFormError from "../AppFormError";

export default {
    name: "Radio",
    components: {AppFormError},
    props: {
        id: Number,
        isRequired: Boolean,
        type: String,
        title: String,
        data: Array,
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