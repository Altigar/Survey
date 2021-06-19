<template>
    <article>
        <h3>{{ title }}</h3>
        <b-form-checkbox v-for="option in data" :key="option.id" :name="`question[${id}][]`" :value="option.id" v-model="value">{{ option.text }}</b-form-checkbox>
    </article>
</template>

<script>
export default {
    name: "Checkbox",
    props: {
        id: Number,
        type: String,
        title: String,
        data: Array,
    },
    data() {
        return {
            value: [],
        };
    },
}
</script>

<style scoped>

</style>