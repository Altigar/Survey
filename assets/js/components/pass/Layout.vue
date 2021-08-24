<template>
    <div>
        <div v-if="error" class="alert alert-danger mt-3" role="alert">Something went wrong</div>
        <div class="card mb-3 mt-3">
            <div class="card-body">
                <h1>{{ surveyName }}</h1>
                <div>{{ surveyDescription }}</div>
            </div>
        </div>
        <form @submit.prevent="save">
            <template v-for="question in data">
                <radio
                    v-if="question.type === 'radio'"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :data="question.options"
                    :key="question.id"
                    ref="question"
                    class="question-pass"
                ></radio>
                <checkbox
                    v-else-if="question.type === 'checkbox'"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :data="question.options"
                    :key="question.id"
                    ref="question"
                    class="question-pass"
                ></checkbox>
                <note
                    v-else-if="question.type === 'string'"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :key="question.id"
                    ref="question"
                    class="question-pass"
                ></note>
                <note-area
                    v-else-if="question.type === 'text'"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :rows="question.row"
                    :key="question.id"
                    ref="question"
                    class="question-pass"
                ></note-area>
                <scale
                    v-else-if="question.type === 'scale'"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :amount="question.scale"
                    :message-from="question.scaleFromText"
                    :message-to="question.scaleToText"
                    :key="question.id"
                    ref="question"
                    class="question-pass"
                ></scale>
            </template>
            <hr>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</template>

<script>
import axios from "../../axios";
import Radio from "./Radio";
import Checkbox from "./Checkbox";
import Note from "./Note";
import NoteArea from "./NoteArea";
import Scale from "./Scale";

export default {
    name: "Base",
    components: {Scale, NoteArea, Radio, Checkbox, Note},
    props: {
        id: String,
        surveyName: String,
        surveyDescription: String,
        surveyHash: String,
        questions: String,
    },
    data() {
        return {
            data: null,
            error: false,
        };
    },
    methods: {
        async save() {
            let requestData = [];
            for (let question of this.$refs.question) {
                let data = {id: question.id, is_required: question.isRequired, type: question.type, answers: []};
                if (question.value || (Array.isArray(question.value) && question.value.length > 0)) {
                    switch (question.type) {
                        case 'radio':
                            data['answers'] = [{option: {id: question.value}}];
                            break;
                        case 'scale':
                            data['answers'] = [{option: {id: question.optionId}, scale_value: question.value}];
                            break;
                        case 'checkbox':
                            let options = [];
                            for (let optionId of question.value) {
                                options.push({option: {id: optionId}});
                            }
                            data['answers'] = options;
                            break;
                        case 'string':
                        case 'text':
                            data['answers'] = [{option: {id: question.optionId}, text: question.value}];
                            break;
                    }
                }
                requestData.push(data);
            }
            this.error = false;
            this.$refs.question.map(elem => elem.error = null);
            try {
                await axios.post(`/pass/${this.surveyHash}`, requestData);
                window.location.href = '/';
            } catch (error) {
                if (error.response.status === 422) {
                    let data = error.response.data;
                    for (let violationKey of Object.keys(data)) {
                        for (let question of this.$refs.question) {
                            if (Number(violationKey) === Number(question.id)) {
                                question.error = data[violationKey].error;
                            }
                        }
                    }
                } else {
                    this.error = true;
                }
            }
        },
    },
    mounted() {
        this.data = JSON.parse(this.questions);
    }
}
</script>

<style scoped>
    .question-pass {
        margin-bottom: 1rem;
    }

    .question-pass:last-child {
        margin-bottom: 0;
    }
</style>