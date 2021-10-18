<template>
    <div>
        <app-error v-if="error" @close="closeError">{{ error }}</app-error>
        <div class="row min-w-100">
            <div class="col-md-3 mb-4">
                <sidebar @add="add" :data="types"></sidebar>
            </div>
            <section class="col-md-9">
                <template v-for="question in data">
                    <choice
                        :key="question.id"
                        :survey-id="id"
                        :data="question"
                        :ordering="question.ordering"
                        v-if="isSelected(question.type, ['radio', 'checkbox'])"
                        @remove="remove"
                        ref="question"
                        class="question-content shadow-sm"
                        @showError="showError"
                    ></choice>
                    <note
                        :key="question.id"
                        :survey-id="id"
                        :data="question"
                        v-if="isSelected(question.type, ['string'])"
                        @remove="remove"
                        ref="question"
                        class="question-content shadow-sm"
                        @showError="showError"
                    ></note>
                    <note-area
                        :key="question.id"
                        :survey-id="id"
                        :data="question"
                        v-if="isSelected(question.type, ['text'])"
                        @remove="remove"
                        ref="question"
                        class="question-content shadow-sm"
                        @showError="showError"
                    ></note-area>
                    <scale
                        :key="question.id"
                        :survey-id="id"
                        :data="question"
                        v-if="isSelected(question.type, ['scale'])"
                        @remove="remove"
                        ref="question"
                        class="question-content shadow-sm"
                        @showError="showError"
                    ></scale>
                </template>
            </section>
        </div>
    </div>
</template>

<script>
import axios from "../../axios";
import AppError from "../AppError";
import Choice from "./Choice";
import Note from "./Note";
import Sidebar from "./Sidebar";
import Scale from "./Scale";
import NoteArea from "./NoteArea";

export default {
    name: "Layout",
    components: {AppError, NoteArea, Scale, Sidebar, Choice, Note},
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
            let responseCreate;
            try {
                responseCreate = await axios.post(`/content/${this.id}`, {
                    survey: Number(this.id),
                    type: event.value,
                    ordering: number
                });
            } catch (error) {
                this.showError('Failed to create question');
                return;
            }
            try {
                let response = await axios.get(`/content/${this.id}`);
                this.data = response.data;
                this.$nextTick(() => {
                    let question = this.$refs.question.find(elem => elem.data.id === responseCreate.data.id);
                    if (question) {
                        question.edited = true;
                    }
                });
            } catch (error) {
                if (error.response.status === 422) {
                    this.error = error.response.data.error;
                } else {
                    this.showError('Failed to load data');
                }
            }
        },
        async remove(id) {
            if (!confirm('Are you sure you want to delete this question? All answers related to this question will be deleted.')) {
                return;
            }
            try {
                await axios.delete(`/content/${id}`);
                let response = await axios.get(`/content/${this.id}`);
                this.data = response.data;
            } catch (error) {
                this.error = error.response.data;
            }
        },
        isSelected(type, options) {
            return options.includes(type);
        },
        closeError() {
            this.error = null;
        },
        showError(error) {
            this.error = error;
        },
    },
    created() {
        this.data = JSON.parse(this.questions);
    }
}
</script>

<style scoped>
    .question-content {
        margin-bottom: 1rem;
    }

    .question-content:last-child {
        margin-bottom: 0;
    }
</style>