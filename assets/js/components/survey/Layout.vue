<template>
    <b-row class="min-w-100">
        <b-col class="mb-4" tag="aside" cols="3">
            <sidebar @add="add" :data="types"></sidebar>
        </b-col>
        <b-col tag="section" cols="9">
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
                    v-if="isSelected(question.type, ['string'])"
                    @remove="remove"
                    ref="question"
                ></note>
                <note-area
                    :key="question.id"
                    :survey-id="id"
                    :data="question"
                    v-if="isSelected(question.type, ['text'])"
                    @remove="remove"
                    ref="question"
                ></note-area>
                <scale
                    :key="question.id"
                    :survey-id="id"
                    :data="question"
                    v-if="isSelected(question.type, ['scale'])"
                    @remove="remove"
                    ref="question"
                ></scale>
            </template>
            <div>
                <b-alert show dismissible v-if="error">{{ error.text }}</b-alert>
            </div>
        </b-col>
    </b-row>
</template>

<script>
import axios from "axios";
import Choice from "./Choice";
import Note from "./Note";
import Sidebar from "./Sidebar";
import Scale from "./Scale";
import NoteArea from "./NoteArea";

export default {
    name: "Layout",
    components: {NoteArea, Scale, Sidebar, Choice, Note},
    props: {
        id: String,
        questions: String,
    },
    data() {
        return {
            error: null,
            data: [],
            types: [
                {value: 'radio'},
                {value: 'checkbox'},
                {value: 'string'},
                {value: 'text'},
                {value: 'scale'},
            ],
        }
    },
    methods: {
        async add(event) {
            let number = 1;
            if (Array.isArray(this.$refs.question) && this.$refs.question.length > 0) {
                number = Math.max(...this.$refs.question.map(elem => elem.$props.data.ordering)) + 1;
            }
            try {
                let responseCreate = await axios.post(`/content/${this.id}`, {type: event.value, ordering: number});
                let response = await axios.get(`/content/${this.id}`, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                this.data = response.data;
                this.$nextTick(() => {
                    let question = this.$refs.question.find(elem => elem.data.id === responseCreate.data.data.id);
                    if (question) {
                        question.edited = true;
                    }
                });
            } catch (error) {
                this.error = error.response.data;
            }
        },
        async remove(id) {
            try {
                await axios.delete(`/content/${id}`);
                let response = await axios.get(`/content/${this.id}`, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                this.data = response.data;
            } catch (error) {
                this.error = error.response.data;
            }
        },
        isSelected(type, options) {
            return options.includes(type);
        },
    },
    created() {
        this.data = JSON.parse(this.questions);
    }
}
</script>

<style scoped>

</style>