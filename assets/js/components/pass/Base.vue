<template>
    <b-form method="post" @submit.prevent="save">
        <b-form-group v-for="question in data" :key="question.id">
            <radio v-if="question.type === 'radio'" :id="question.id" :title="question.text" :data="question.options" ref="question"></radio>
            <checkbox v-else-if="question.type === 'checkbox'" :id="question.id" :title="question.text" :data="question.options" ref="question"></checkbox>
            <note v-else-if="question.type === 'string'" :id="question.id" :title="question.text" ref="question"></note>
            <note-area v-else-if="question.type === 'text'" :id="question.id" :title="question.text" :rows="question.row" ref="question"></note-area>
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
            console.log(this.$refs);
            let data = {};
            for (let question of this.$refs.question) {
                data[question.$props.id] = question.value;
            }
            try {
                await axios.post(`/pass/${this.id}/create`, data);
            } catch (error) {
                let data = error.response.data;
            }
        },
    },
    created() {
        this.data = JSON.parse(this.questions);
    }
}
</script>

<style scoped>

</style>