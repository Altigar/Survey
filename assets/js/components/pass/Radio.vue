<template>
    <article>
        <h3>{{ title }}</h3>
        <b-form-radio v-for="option in data" :key="option.id" :name="`question[${id}]`" :value="option.id" v-model="value">{{ option.text }}</b-form-radio>
        <small v-if="error" class="text-danger">{{ error }}</small>
    </article>
</template>

<script>
export default {
    name: "Radio",
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