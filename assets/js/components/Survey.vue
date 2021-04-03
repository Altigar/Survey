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
                :ordering="question.ordering"
                @remove="remove"
                ref="question"
            ></choice>
            <string :key="question.id" v-if="question.type === 'string'" :data="question" :type="question.type" :survey-id="id" :id="question.id"></string>
        </template>
        <b-form-select v-model="selected" :options="options" size="sm"></b-form-select>
        <b-button @click="add">add</b-button>
        <div>
            <b-alert show dismissible v-if="error">{{ error.text }}</b-alert>
        </div>
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
            selected: null,
            error: null,
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
            } catch (error) {
                this.error = error.response.data;
            }
        },
        async add() {
            let number = Math.max(...this.$refs.question.map(elem => elem.$props.data.ordering));
            try {
                await axios.post(`/survey/plan/${this.id}/add`, {type: this.selected, ordering: ++number});
            } catch (error) {
                this.error = error.response.data;
            }
            await this.fetchAll();
        },
        async remove(id) {
            this.clearErrors(id);
            try {
                await axios.delete(`/survey/plan/${this.id}/remove`, {data: {id: id}});
            } catch (error) {
                this.error = error.response.data;
            }
            if (!this.hasError()) {
                await this.fetchAll();
            }
        },
        isSelected(type, options) {
            return options.includes(type);
        },
        clearErrors(id) {
            let question = this.data.find(elem => elem.id === id);
            question.options.forEach(elem => elem.error = null);
        },
        hasError() {
            return Boolean(this.error);
        }
    },
    created() {
        this.data = JSON.parse(this.json);
    }
}
</script>

<style scoped>

</style>