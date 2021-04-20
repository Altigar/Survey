<template>
    <article>
        <h3>{{ title }}</h3>
        <b-form-input :key="id" :name="`question[${id}]`" v-model="value"></b-form-input>
    </article>
</template>

<script>
export default {
    name: "Note",
    props: {
        id: Number,
        title: String,
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