<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form method="post">
                <b-form-group>
                    <b-form-input class="mb-3" v-model="data.text"></b-form-input>
                    <p v-if="data.error">{{ data.error }}</p>
                    <div v-for="(option, index) in sortedOptions" :key="index">
                        <div class="d-flex mb-3">
                            <b-form-input v-model="option.text" size="sm" class="mr-3"></b-form-input>
                            <button @click="remove(index)" type="button" class="close" aria-label="Close">×</button>
                        </div>
                        <p v-if="option.error">{{ option.error }}</p>
                    </div>
                </b-form-group>
                <div>
                    <b-btn @click="add">add</b-btn>
                    <b-btn @click="save">save</b-btn>
                    <b-btn @click="$emit('remove', data.id)">remove</b-btn>
                </div>
            </b-form>
        </div>
    </article>
</template>

<script>
import axios from "axios";

export default {
    name: "Choice",
    props: {
        surveyId: String,
        data: Object,
    },
    computed: {
        sortedOptions() {
            return this.data.options.sort((a, b) => a.ordering - b.ordering);
        }
    },
    methods: {
        add() {
            let orders = [];
            for (let option of this.data.options) {
                orders.push(option.ordering);
            }
            this.data.options.push({text: '', error: '', ordering: Math.max(...orders) + 1});
        },
        remove(number) {
            this.data.options = this.data.options.filter((elem, index) => index !== number);
            let order = 1;
            for (let option of this.data.options) {
                option.ordering = order;
                order++;
            }
        },
        async save() {
            this.data.error = '';
            for (let option of this.data.options) {
                option.error = '';
            }
            try {
                await axios.put(`/content/${this.data.id}`, this.data);
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
                            this.data.options[nestedKey].error = data[key][nestedKey].text;
                        }
                    } else {
                        this.data.error = data.text;
                    }
                }
            }
            this.$forceUpdate();
        }
    }
}
</script>

<style scoped>

</style>