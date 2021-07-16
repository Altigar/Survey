<template>
    <article>
        <h3>{{ title }}</h3>
        <b-form-input :key="id" :name="`question[${id}]`" v-model="value"></b-form-input>
        <small v-if="error" class="text-danger">{{ error }}</small>
    </article>
</template>

<script>
export default {
    name: "Note",
    props: {
        id: Number,
        isRequired: Boolean,
        type: String,
        optionId: Number,
        title: String,
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