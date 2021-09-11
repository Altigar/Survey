<template>
    <div class="card shadow-sm">
        <div class="card-body">
            <h4>{{ title }}<span v-if="isRequired" class="text-danger ms-2">*</span></h4>
            <div class="d-flex justify-content-between mb-2">
                <span>{{ messageFrom }}</span>
                <span>{{ messageTo }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <div v-for="number in amount" :key="number" class="form-check">
                    <input v-model="value" :value="number" class="form-check-input" type="radio" :id="`question[${id}][${number}]`" :name="`question[${id}]`">
                    <label class="form-check-label" :for="`question[${id}][${number}]`">{{ number }}</label>
                </div>
            </div>
            <app-form-error v-if="error" class="text-danger">{{ error }}</app-form-error>
        </div>
    </div>
</template>

<script>
import AppFormError from "../AppFormError";

export default {
    name: "Scale",
    components: {AppFormError},
    props: {
        id: Number,
        isRequired: Boolean,
        type: String,
        title: String,
        amount: Number,
        messageFrom: String,
        messageTo: String,
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