<template>
    <div>
        <template v-for="question in questions">
            <choice
                :key="question.id"
                v-if="isSelected(question.type, ['radio', 'checkbox'])"
                :data="question"
                :type="question.type"
                :survey-id="id"
                :id="question.id"
                @remove="remove"
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
import {mapActions, mapGetters} from "vuex";

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
    computed: {
        ...mapGetters({
            questions: 'question/getItems',
            hasErrors: 'question/hasErrors',
            error: 'question/error'
        })
    },
    methods: {
        ...mapActions({
            request: 'question/request',
            save: 'question/save',
            delete: 'question/delete',
            clearErrors: 'question/clearErrors',
        }),
        async add() {
            try {
                await axios.post(`/survey/plan/${this.id}/add`, {type: this.selected});
            } catch (error) {
                //TODO:
                console.log(error.response)
            }
            await this.request(this.id);
        },
        async remove(id) {
            this.clearErrors(id);
            await this.delete(id);
            if (!this.hasErrors) {
                await this.request(this.id);
            }
        },
        isSelected(type, options) {
            return options.includes(type);
        },
    },
    created() {
        this.save(JSON.parse(this.json));
    }
}
</script>

<style scoped>

</style>