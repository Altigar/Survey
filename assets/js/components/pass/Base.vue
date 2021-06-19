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
                v-else-if="question.type === 'string' && getFirstOptionId(question)"
                :id="question.id"
                :title="question.text"
                :type="question.type"
                :option-id="getFirstOptionId(question)"
                ref="question"
            ></note>
            <note-area
                v-else-if="question.type === 'text' && getFirstOptionId(question)"
                :id="question.id"
                :title="question.text"
                :type="question.type"
                :option-id="getFirstOptionId(question)"
                :rows="question.row"
                ref="question"
            ></note-area>
        </b-form-group>
        <hr>
        <b-btn type="submit">submit</b-btn>
    </b-form>
</template>

<script>
import Radio from "./Radio";
import Checkbox from "./Checkbox";
import Note from "./Note";
import NoteArea from "./NoteArea";
import axios from "axios";

export default {
    name: "Base",
    components: {NoteArea, Radio, Checkbox, Note},
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
                    data.push({id: question.$props.id, answers: [{option: {id: question.value}}]});
                } else if (question.type === 'checkbox') {
                    let options = [];
                    for (let optionId of question.value) {
                        options.push({option: {id: optionId}});
                    }
                    data.push({id: question.$props.id, answers: options});
                } else if (['string', 'text'].includes(question.type)) {
                    data.push({id: question.$props.id, answers: [{option: {id: question.$props.optionId}, text: question.value}]});
                }
            }
            try {
                await axios.post(`/pass/${this.id}`, data);
            } catch (error) {
                let data = error.response.data;
            }
        },
        getFirstOptionId(question) {
            if (question.options[0] && question.options[0].hasOwnProperty('id')) {
                return question.options[0].id;
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