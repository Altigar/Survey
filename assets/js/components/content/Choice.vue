<template>
    <div class="card pr-0 mb-4 bg-white rounded border" @click="edited = true">
        <div class="card-body">
            <div v-if="!edited">
                <h3>{{ data.text }}</h3>
                <div v-for="option in sortedOptions" :key="option.id" class="form-check">
                    <input v-if="data.type === 'radio'" class="form-check-input" type="radio">
                    <input v-else-if="data.type === 'checkbox'" class="form-check-input" type="checkbox">
                    <label class="form-check-label">{{ option.text }}</label>
                </div>
            </div>
            <form method="post" v-else-if="edited">
                <div class="mb-2">
                    <input v-model="data.text" type="text" class="form-control mb-3">
                    <p v-if="data.error">{{ data.error }}</p>
                    <div v-for="(option, index) in sortedOptions" :key="index">
                        <div class="d-flex mb-2">
                            <input type="text" v-model="option.text" class="form-control me-3" size="sm">
                            <button @click="remove(index)" type="button" class="btn-close align-self-center" aria-label="Close"></button>
                        </div>
                        <app-form-error v-if="option.error">{{ option.error }}</app-form-error>
                    </div>
                    <div class="mb-2">
                        <a @click.prevent="add" class="pointer text-decoration-none"><span style="font-weight: bold; font-size: 25px;">+</span> Add new option</a>
                    </div>
                    <v-switch :id="switch_id" v-model="data.isRequired">Required</v-switch>
                </div>
                <v-footer @save="save" @remove="$emit('remove', data.id)" @edit.stop="edited = false"></v-footer>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "../../axios";
import Base from "./Base";
import VSwitch from "../VSwitch";
import VFooter from "./VFooter";
import AppFormError from "../AppFormError";

export default {
    name: "Choice",
    mixins: [Base],
    components: {AppFormError, VFooter, VSwitch},
    props: {
        surveyId: String,
        data: Object,
    },
    data() {
        return {
            switch_id: null,
        }
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
                this.edited = false;
            } catch (error) {
                let data = error.response.data;
                for (let key of Object.keys(data)) {
                    if (typeof data[key] === 'object') {
                        for (let nestedKey of Object.keys(data[key])) {
                            this.data.options[nestedKey].error = data[key][nestedKey].text;
                        }
                    } else {
                        this.data.error = data.text;
                    }
                }
            }
            this.$forceUpdate();
        }
    },
    created() {
        this.switch_id = `switch_${this.data.id}`;
    }
}
</script>

<style scoped>

</style>