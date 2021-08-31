<template>
    <div @click="edited = true" class="card">
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
                    <input v-model="data.text" type="text" class="form-control mb-2">
                    <app-form-error v-if="errors.text" class="mb-2">{{ errors.text }}</app-form-error>
                    <div v-for="(option, index) in sortedOptions" :key="index">
                        <div class="d-flex mb-2">
                            <input type="text" v-model="option.text" class="form-control me-3" size="sm">
                            <button @click="remove(index)" type="button" class="btn-close align-self-center" aria-label="Close"></button>
                        </div>
                        <app-form-error
                            v-if="errors.options &&
                            errors.options[index] &&
                            errors.options[index].text"
                            class="mb-2"
                        >{{ errors.options[index].text }}</app-form-error>
                    </div>
                    <div class="mb-2">
                        <a @click.prevent="add" class="d-flex align-items-center pointer text-decoration-none">
                            <span class="btn-add">+</span>
                            <span>Add new option</span>
                        </a>
                    </div>
                    <app-switch :id="switchId" v-model="data.isRequired">Required</app-switch>
                </div>
                <v-footer @save="save" @remove="$emit('remove', data.id)" @edit.stop="edited = false"></v-footer>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "../../axios";
import Base from "./Base";
import AppSwitch from "../AppSwitch";
import VFooter from "./VFooter";
import AppFormError from "../AppFormError";

export default {
    name: "Choice",
    mixins: [Base],
    components: {AppFormError, VFooter, AppSwitch},
    props: {
        surveyId: String,
        data: Object,
    },
    data() {
        return {
            errors: {}
        }
    },
    computed: {
        sortedOptions() {
            this.errors = {};
            return this.data.options.sort((a, b) => a.ordering - b.ordering);
        },
        switchId() {
            return `switch_${this.data.id}`;
        }
    },
    methods: {
        add() {
            let orders = [];
            for (let option of this.data.options) {
                orders.push(option.ordering);
            }
            this.data.options.push({text: '', ordering: Math.max(...orders) + 1});
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
            this.errors = {};
            try {
                await axios.put(`/content/${this.data.id}`, this.data);
                this.edited = false;
            } catch (error) {
                if (error.response.status === 422) {
                    this.errors = error.response.data;
                } else {
                    this.$emit('showError', 'Something went wrong');
                }
            }
            this.$forceUpdate();
        },
    },
}
</script>

<style scoped>
    .btn-add {
        font-weight: bold;
        font-size: 25px;
        margin-right: 5px;
    }
</style>