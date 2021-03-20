<template>
    <div>
        <template v-if="data">
            <template v-for="question in data">
                <radio :key="question.id" v-if="question.type === 'radio'" :data="question" :type="question.type" :survey-id="surveyId" :question-id="question.id"></radio>
                <checkbox :key="question.id" v-if="question.type === 'checkbox'" :data="question" :type="question.type" :survey-id="surveyId" :question-id="question.id"></checkbox>
                <string :key="question.id" v-if="question.type === 'string'" :data="question" :type="question.type" :survey-id="surveyId" :question-id="question.id"></string>
            </template>
        </template>
        <template v-if="selectedOptions">
            <template v-for="type in selectedOptions">
                <radio v-if="type === 'radio'" :type="type" :survey-id="surveyId"></radio>
                <checkbox v-if="type === 'checkbox'" :type="type" :survey-id="surveyId"></checkbox>
                <string v-if="type === 'string'" :type="type" :survey-id="surveyId"></string>
            </template>
        </template>

        <b-form-select v-model="selected" :options="options" size="sm"></b-form-select>
        <b-button @click="add">add</b-button>
        <b-button @click="remove" v-if="amount > 0">remove</b-button>
    </div>
</template>

<script>
import Radio from "./Radio";
import String from "./String";
import Checkbox from "./Checkbox";

export default {
    name: "Survey",
    components: {Checkbox, String, Radio},
    props: {
        surveyId: String,
        json: String,
    },
    data() {
        return {
            selected: '',
            amount: 1,
            data: [],
            options: [
                {value: 'radio', text: 'radio'},
                {value: 'checkbox', text: 'checkbox'},
                {value: 'string', text: 'string'}
            ],
            selectedOptions: []
        }
    },
    methods: {
        add() {
            this.selectedOptions.push(this.selected);
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