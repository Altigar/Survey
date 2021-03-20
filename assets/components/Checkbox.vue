<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form method="post">
                <b-form-group>
                    <b-form-input class="mb-3" v-model="question.text"></b-form-input>
                    <p v-if="question.error">{{ question.error }}</p>
                    <div v-for="(option, index) in options" :key="index">
                        <b-form-input v-model="option.text" size="sm"></b-form-input>
                        <p v-if="option.error">{{ option.error }}</p>
                        <b-btn @click="remove(index)">remove</b-btn>
                    </div>
                </b-form-group>
                <div>
                    <b-btn @click="add">add</b-btn>
                    <b-btn @click="save">save</b-btn>
                </div>
            </b-form>
        </div>
    </article>
</template>

<script>
import axios from "axios";

export default {
    name: "Checkbox",
    props: {
        surveyId: String,
        questionId: Number,
        data: Object,
        type: String,
    },
    data() {
        return {
            question: [{text: '', error: ''}],
            options: [
                {text: '', error: ''},
                {text: '', error: ''}
            ],
        };
    },
    methods: {
        add() {
            this.options.push({text: '', error: ''});
        },
        remove(number) {
            this.options = this.options.filter((elem, index) => index !== number);
        },
        async save() {
            this.question.error = '';
            for (let option of this.options) {
                option.error = '';
            }
            let question = {
                id: this.questionId,
                type: this.type,
                text: this.question.text,
                options: this.options,
            };
            try {
                await axios.post(`/survey/plan/${this.surveyId}`, question);
            } catch (error) {
                let data = error.response.data;
                for (let value in data) {
                    if (data.hasOwnProperty(value)) {
                        let matches = value.match(/options\[(?<index>\d+)\]/);
                        if (matches && Object.prototype.hasOwnProperty.call(matches.groups, 'index')) {
                            let i = matches.groups.index;
                            this.options[i].error = data[value];
                        }
                        if (value === 'text') {
                            this.question.error = data[value];
                        }
                    }
                }
            }
        }
    },
    created() {
        if (this.data) {
            this.question.text = this.data.text;
            this.options = this.data.options;
        }
    }
}
</script>

<style scoped>

</style>