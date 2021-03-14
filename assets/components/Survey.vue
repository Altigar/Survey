<template>
    <div>
        <question v-for="(number, index) in amount" :key="index" :id="number"></question>
        <b-button @click="add">add</b-button>
        <b-button @click="remove">remove</b-button>
    </div>
</template>

<script>
import Question from "./Question";

export default {
    name: "Survey",
    components: {Question},
    data() {
        return {
            amount: 1,
        }
    },
    methods: {
        add() {
            this.amount++;
        },
        remove() {
            this.amount--;
        },
    }
}
</script>

<style scoped>

</style>