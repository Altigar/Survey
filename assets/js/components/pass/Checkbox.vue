<template>
    <article>
        <h3>{{ title }}</h3>
        <b-form-checkbox v-for="option in data" :key="option.id" :name="`question[${id}][]`" :value="option.id" v-model="value">{{ option.text }}</b-form-checkbox>
        <small v-if="error" class="text-danger">{{ error }}</small>
    </article>
</template>

<script>
export default {
    name: "Checkbox",
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