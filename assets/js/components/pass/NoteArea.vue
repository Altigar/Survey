<template>
    <article>
        <h3>{{ title }}</h3>
        <b-form-textarea :key="id" :name="`question[${id}]`" v-model="value" :rows="rows"></b-form-textarea>
        <small v-if="error" class="text-danger">{{ error }}</small>
    </article>
</template>

<script>
export default {
    name: "NoteArea",
    props: {
        id: Number,
        isRequired: Boolean,
        type: String,
        optionId: Number,
        title: String,
        rows: Number,
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