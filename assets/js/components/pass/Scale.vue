<template>
    <article>
        <h3>{{ title }}</h3>
        <div class="d-flex justify-content-between mb-2">
            <span>{{ messageFrom }}</span>
            <span>{{ messageTo }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <b-form-radio v-for="number in amount" :key="number" :name="`question[${id}]`" :value="number" v-model="value">{{ number }}</b-form-radio>
        </div>
    </article>
</template>

<script>
export default {
    name: "Scale",
    props: {
        id: Number,
        type: String,
        title: String,
        optionId: Number,
        amount: Number,
        messageFrom: String,
        messageTo: String,
    },
    data() {
        return {
            value: null,
        };
    }
}
</script>

<style scoped>

</style>