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
                @remove="remove"
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
            data: [],
            options: [
                {value: 'radio', text: 'radio'},
                {value: 'checkbox', text: 'checkbox'},
                {value: 'string', text: 'string'}
            ],
        }
    },
    methods: {
        async fetchAll() {
            try {
                let response = await axios.get(`/survey/plan/${this.id}/all`);
                this.data = JSON.parse(response.data);
            } catch(error) {
                //TODO:
                console.log(error.response)
            }
        },
        async add() {
            try {
                await axios.post(`/survey/plan/${this.id}/add`, {type: this.selected});
            } catch (error) {
                //TODO:
                console.log(error.response)
            }
            await this.fetchAll();
        },
        async remove(id) {
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
            await this.fetchAll();
        },
        isSelected(type, options) {
            return options.includes(type);
        },
    },
    created() {
        this.data = JSON.parse(this.json);
    }
}
</script>

<style scoped>

</style>