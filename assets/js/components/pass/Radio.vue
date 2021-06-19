<template>
    <article>
        <h3>{{ title }}</h3>
        <b-form-radio v-for="option in data" :key="option.id" :name="`question[${id}]`" :value="option.id" v-model="value">{{ option.text }}</b-form-radio>
    </article>
</template>

<script>
export default {
    name: "Radio",
    props: {
        id: Number,
        type: String,
        title: String,
        data: Array,
    },
    data() {
        return {
            value: null,
        };
    },
}
</script>

<style scoped>

</style>