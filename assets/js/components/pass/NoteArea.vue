<template>
    <article>
        <h3>{{ title }}</h3>
        <b-form-textarea :key="id" :name="`question[${id}]`" v-model="value" :rows="rows"></b-form-textarea>
    </article>
</template>

<script>
export default {
    name: "NoteArea",
    props: {
        id: Number,
        type: String,
        optionId: Number,
        title: String,
        rows: Number,
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