<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form method="post">
                <b-form-group>
                    <b-form-input class="mb-3" v-model="question.text"></b-form-input>
                    <p v-if="question.error">{{ question.error }}</p>
                    <div v-for="(option, index) in sortedOptions" :key="index">
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
    name: "Radio",
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
                {text: '', error: '', ordering: 1},
                {text: '', error: '', ordering: 2}
            ],
        };
    },
    methods: {
        add() {
            let orders = [];
            for (let option of this.options) {
                orders.push(option.ordering);
            }
            this.options.push({text: '', error: '', ordering: Math.max(...orders) + 1});
        },
        remove(number) {
            this.options = this.options.filter((elem, index) => index !== number);
            let order = 1;
            for (let option of this.options) {
                option.ordering = order;
                order++;
            }
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
                for (let key in data) {
                    if (!data.hasOwnProperty(key)) {
                        continue;
                    }
                    if (typeof data[key] === 'object') {
                        let nestedData = data[key];
                        for (let nestedKey in nestedData) {
                            if (!nestedData.hasOwnProperty(nestedKey)) {
                                continue;
                            }
                            this.options[nestedKey].error = data[key][nestedKey].text;
                        }
                    } else {
                        this.question.error = data.text;
                    }
                }
            }
        }
    },
    computed: {
        sortedOptions() {
            return this.options.sort((a, b) => a.ordering - b.ordering);
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