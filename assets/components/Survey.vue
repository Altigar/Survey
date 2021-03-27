<template>
    <div>
        <template v-if="data">
            <template v-for="question in data">
                <radio
                    :key="'id:' + question.id"
                    v-if="question.type === 'radio'"
                    :data="question" :type="question.type"
                    :survey-id="id"
                    :question-id="question.id"
                    @purge="purge"
                    ref="question"
                ></radio>
                <checkbox
                    :key="'id:' + question.id"
                    v-if="question.type === 'checkbox'"
                    :data="question"
                    :type="question.type"
                    :survey-id="id"
                    :question-id="question.id"
                    @purge="purge"
                    ref="question"
                ></checkbox>
                <string :key="'id:' + question.id" v-if="question.type === 'string'" :data="question" :type="question.type" :survey-id="id" :question-id="question.id"></string>
            </template>
        </template>
        <template v-if="selectedOptions">
            <template v-for="(type, index) in selectedOptions">
                <radio :key="index" v-if="type === 'radio'" :type="type" :survey-id="id" :index="index" @remove="remove"></radio>
                <checkbox :key="index" v-if="type === 'checkbox'" :type="type" :survey-id="id" :index="index" @remove="remove"></checkbox>
                <string :key="index" v-if="type === 'string'" :type="type" :survey-id="id"></string>
            </template>
        </template>
        <b-form-select v-model="selected" :options="options" size="sm"></b-form-select>
        <b-button @click="add">add</b-button>
    </div>
</template>

<script>
import Radio from "./Radio";
import String from "./String";
import Checkbox from "./Checkbox";
import axios from "axios";

export default {
    name: "Survey",
    components: {Checkbox, String, Radio},
    props: {
        id: String,
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
        remove(id) {
            this.selectedOptions.splice(id, id + 1);
        },
        async purge(id) {
            let question;
            for (let component of this.$refs.question) {
                if (component.$props.questionId === id) {
                    question = component;
                    break;
                }
            }
            question.question.error = '';
            try {
                await axios.delete(`/survey/plan/${this.surveyId}/remove`, {data: {id: id}});
            } catch (error) {
                question.question.error = error.response.data.text;
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