<template>
    <div>
        <template v-for="question in data">
            <choice
                :key="question.id"
                :survey-id="id"
                :data="question"
                :ordering="question.ordering"
                v-if="isSelected(question.type, ['radio', 'checkbox'])"
                @remove="remove"
                ref="question"
            ></choice>
            <note
                :key="question.id"
                :survey-id="id"
                :data="question"
                v-if="isSelected(question.type, ['string', 'text'])"
                @remove="remove"
                ref="question"
            ></note>
        </template>
        <b-form-select v-model="selected" :options="types" size="sm"></b-form-select>
        <b-button @click="add">add</b-button>
        <div>
            <b-alert show dismissible v-if="error">{{ error.text }}</b-alert>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Choice from "./Choice";
import Note from "./Note";

export default {
    name: "Base",
    components: {Choice, Note},
    props: {
        id: String,
        questions: String,
        options: String,
    },
    data() {
        return {
            selected: null,
            error: null,
            data: [],
            types: [],
        }
    },
    methods: {
        async add() {
            let number = 1;
            if (this.$refs.question) {
                number = Math.max(...this.$refs.question.map(elem => elem.$props.data.ordering)) + 1;
            }
            try {
                let response = await axios.post(`/content/${this.id}/create`, {type: this.selected, ordering: number});
                this.data = response.data;
            } catch (error) {
                this.error = error.response.data;
            }
        },
        async remove(id) {
            this.clearErrors(id);
            try {
                let response = await axios.delete(`/content/${this.id}/remove`, {data: {id: id}});
                this.data = response.data;
            } catch (error) {
                this.error = error.response.data;
            }
        },
        isSelected(type, options) {
            return options.includes(type);
        },
        clearErrors(id) {
            let question = this.data.find(elem => elem.id === id);
            question.options.forEach(elem => elem.error = null);
        },
    },
    created() {
        this.data = JSON.parse(this.questions);
        this.types = JSON.parse(this.options);
    }
}
</script>

<style scoped>

</style>