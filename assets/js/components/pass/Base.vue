<template>
    <b-form method="post" @submit.prevent="save">
        <b-form-group v-for="question in data" :key="question.id">
            <radio
                v-if="question.type === 'radio'"
                :id="question.id"
                :title="question.text"
                :type="question.type"
                :data="question.options"
                ref="question"
            ></radio>
            <checkbox
                v-else-if="question.type === 'checkbox'"
                :id="question.id"
                :title="question.text"
                :type="question.type"
                :data="question.options"
                ref="question"
            ></checkbox>
            <note
                v-else-if="question.type === 'string' && getFirstOptionAttribute(question, 'id')"
                :id="question.id"
                :title="question.text"
                :type="question.type"
                :option-id="getFirstOptionAttribute(question, 'id')"
                ref="question"
            ></note>
            <note-area
                v-else-if="question.type === 'text' && getFirstOptionAttribute(question, 'id')"
                :id="question.id"
                :title="question.text"
                :type="question.type"
                :option-id="getFirstOptionAttribute(question, 'id')"
                :rows="question.row"
                ref="question"
            ></note-area>
            <scale
                v-else-if="question.type === 'scale' && getFirstOptionAttribute(question, 'scale')"
                :id="question.id"
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
</template>

<script>
import axios from "axios";
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
        };
    },
    methods: {
        async save() {
            let data = [];
            for (let question of this.$refs.question) {
                if (question.type === 'radio') {
                    data.push({id: question.id, answers: [{option: {id: question.value}}]});
                } else if (question.type === 'checkbox') {
                    let options = [];
                    for (let optionId of question.value) {
                        options.push({option: {id: optionId}});
                    }
                    data.push({id: question.id, answers: options});
                } else if (['string', 'text'].includes(question.type)) {
                    data.push({id: question.id, answers: [{option: {id: question.optionId}, text: question.value}]});
                } else if (question.type === 'scale') {
                    data.push({id: question.id, answers: [{option: {id: question.optionId}, scale_value: question.value}]});
                }
            }
            try {
                await axios.post(`/pass/${this.id}`, data);
            } catch (error) {
                let data = error.response.data;
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