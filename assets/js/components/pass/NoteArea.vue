<template>
    <article>
        <h3>{{ title }}</h3>
        <b-form-textarea :key="id" :name="`question[${id}]`" v-model="value"></b-form-textarea>
    </article>
</template>

<script>
export default {
    name: "NoteArea",
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