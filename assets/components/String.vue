<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form method="post">
                <b-form-group>
                    <b-form-input class="mb-3" v-model="question.text" value="question" placeholder="question"></b-form-input>
                    <p v-if="question.error">{{ question.error }}</p>
                    <div v-for="(option, index) in options" :key="index">
                        <b-form-input v-model="option.text" size="sm"></b-form-input>
                        <p v-if="option.error">{{ option.error }}</p>
                    </div>
                </b-form-group>
                <b-btn @click="save">save</b-btn>
            </b-form>
        </div>
    </article>
</template>

<script>
import axios from "axios";

export default {
    name: "String",
    props: {
        surveyId: String,
        id: Number,
        data: Object,
        type: String,
    },
    data() {
        return {
            question: [{text: '', error: ''}],
            options: [{text: '', error: ''}],
        };
    },
    methods: {
        async save() {
            this.question.error = '';
            for (let option of this.options) {
                option.error = '';
            }
            let question = {
                id: this.id,
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