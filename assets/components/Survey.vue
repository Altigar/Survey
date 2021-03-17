<template>
    <div>
        <question v-for="value in data" :key="data.id" :id="id" :json="json" :data="value"></question>
        <question v-for="(value, index) in amount" :id="id"></question>
        <b-button @click="add">add</b-button>
        <b-button @click="remove" v-if="amount > 0">remove</b-button>
    </div>
</template>

<script>
import Question from "./Question";

export default {
    name: "Survey",
    components: {Question},
    props: {
        id: String,
        json: String,
    },
    data() {
        return {
            amount: 1,
            data: [],
        }
    },
    methods: {
        add() {
            this.amount++;
        },
        remove() {
            if (this.amount > 0) {
                this.amount--;
            }
        },
    },
    created() {
        this.data = JSON.parse(this.json);
        if (this.data.length > 0) {
            this.amount = 0;
        }
    }
}
</script>

<style scoped>

</style>