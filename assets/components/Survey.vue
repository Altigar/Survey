<template>
    <div>
        <template v-for="question in data">
            <choice
                :key="question.id"
                v-if="isSelected(question.type, ['radio', 'checkbox'])"
                :data="question"
                :type="question.type"
                :survey-id="id"
                :id="question.id"
                @purge="purge"
                ref="question"
            ></choice>
            <string :key="question.id" v-if="question.type === 'string'" :data="question" :type="question.type" :survey-id="id" :id="question.id"></string>
        </template>
        <b-form-select v-model="selected" :options="options" size="sm"></b-form-select>
        <b-button @click="add">add</b-button>
    </div>
</template>

<script>
import axios from "axios";
import String from "./String";
import Choice from "./Choice";

export default {
    name: "Survey",
    components: {Choice, String},
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
        async add() {
            this.selectedOptions.push(this.selected);
            try {
                await axios.post(`/survey/plan/${this.id}/add`, {type: this.selected});
            } catch (error) {
                //TODO:
                console.log(error.response)
            }
        },
        remove(id) {
            this.selectedOptions.splice(id, id + 1);
        },
        async purge(id) {
            let question;
            for (let component of this.$refs.question) {
                if (component.$props.id === id) {
                    question = component;
                    break;
                }
            }
            question.value.error = '';
            try {
                await axios.delete(`/survey/plan/${this.surveyId}/remove`, {data: {id: id}});
            } catch (error) {
                question.value.error = error.response.data.text;
            }
        },
        isSelected(type, options) {
            return options.includes(type);
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