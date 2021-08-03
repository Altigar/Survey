<template>
    <div>
        <div v-if="error" class="alert alert-danger" role="alert">Something went wrong</div>
        <b-form method="post" @submit.prevent="save">
            <b-form-group v-for="question in data" :key="question.id">
                <span style="color: red;">{{question.id}}</span>
                <radio
                    v-if="question.type === 'radio'"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :data="question.options"
                    ref="question"
                ></radio>
                <checkbox
                    v-else-if="question.type === 'checkbox'"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :data="question.options"
                    ref="question"
                ></checkbox>
                <note
                    v-else-if="question.type === 'string' && getFirstOptionAttribute(question, 'id')"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :option-id="getFirstOptionAttribute(question, 'id')"
                    ref="question"
                ></note>
                <note-area
                    v-else-if="question.type === 'text' && getFirstOptionAttribute(question, 'id')"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :option-id="getFirstOptionAttribute(question, 'id')"
                    :rows="question.row"
                    ref="question"
                ></note-area>
                <scale
                    v-else-if="question.type === 'scale' && getFirstOptionAttribute(question, 'scale')"
                    :id="question.id"
                    :isRequired="question.isRequired"
                    :title="question.text"
                    :type="question.type"
                    :message-from="getFirstOptionAttribute(question, 'scaleFromText')"
                    :message-to="getFirstOptionAttribute(question, 'scaleToText')"
                    :option-id="getFirstOptionAttribute(question, 'id')"
                    :amount="getFirstOptionAttribute(question, 'scale')"
                    ref="question"
                ></scale>
            </b-form-group>
            <hr>
            <b-btn type="submit">submit</b-btn>
        </b-form>
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
                await axios.post(`/pass/${this.id}`, requestData);
                window.location.href = '/';
            } catch (error) {
                if (error.response.data.status === 422) {
                    let data = error.response.data.errors;
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
        getFirstOptionAttribute(question, attr) {
            if (question.options[0] && question.options[0].hasOwnProperty(attr)) {
                return question.options[0][attr];
            }
        }
    },
    created() {
        this.data = JSON.parse(this.questions);
    }
}
</script>

<style scoped>

</style>