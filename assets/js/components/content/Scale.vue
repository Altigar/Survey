<template>
    <div @click="edited = true" class="card">
        <div class="card-body">
            <div v-if="!edited">
                <h4 class="text-primary">{{ data.text }}</h4>
                <div class="d-flex justify-content-between mb-2">
                    <div><span>{{ data.scaleFromText }}</span></div>
                    <div><span>{{ data.scaleToText }}</span></div>
                </div>
                <div class="d-flex justify-content-around">
                    <div v-for="number in range(1, data.scale, 1)" :key="number" class="form-check">
                        <input class="form-check-input" type="radio">
                        <label class="form-check-label">{{ number }}</label>
                    </div>
                </div>
            </div>
            <form v-else-if="edited" method="post">
                <div class="mb-2">
                    <div class="mb-2">
                        <input v-model="data.text" type="text" class="form-control mb-2" placeholder="Question text">
                        <app-form-error v-if="error">{{ error }}</app-form-error>
                    </div>
                    <select v-model="data.scale" class="form-select form-select-sm mb-2" style="width: 5rem;">
                        <option v-for="option in options">{{ option }}</option>
                    </select>
                    <div class="mb-2">
                        <input v-model="data.scaleFromText" type="text" class="form-control mb-2" placeholder="From">
                        <app-form-error v-if="textFromError">{{ textFromError }}</app-form-error>
                    </div>
                    <div class="mb-2">
                        <input v-model="data.scaleToText" type="text" class="form-control mb-2" placeholder="To">
                        <app-form-error v-if="textToError">{{ textToError }}</app-form-error>
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
    name: "Scale",
    mixins: [Base],
    components: {AppFormError, VFooter, AppSwitch},
    props: {
        data: Object,
    },
    data() {
        return {
            options: this.range(2, 10, 1),
            textFrom: null,
            textTo: null,
            error: null,
            textFromError: null,
            textToError: null,
        };
    },
    computed: {
        switchId() {
            return `switch_${this.data.id}`;
        }
    },
    methods: {
        async save() {
            this.error = null;
            this.textFromError = null;
            this.textToError = null;
            try {
                await axios.put(`/content/${this.data.id}`, {
                    id: this.data.id,
                    type: this.data.type,
                    text: this.data.text,
                    ordering: this.data.ordering,
                    isRequired: this.data.isRequired,
                    scale: Number(this.data.scale),
                    scale_from_text: this.data.scaleFromText,
                    scale_to_text: this.data.scaleToText,
                });
                this.edited = false;
            } catch (error) {
                if (error.response.status === 422) {
                    this.error = error.response.data.text;
                    this.textFromError = error.response.data.scale_from_text;
                    this.textToError = error.response.data.scale_to_text;
                } else {
                    this.$emit('showError', 'Something went wrong');
                }
                this.$forceUpdate();
            }
        },
        range(start, stop, step) {
            return Array.from({length: (stop - start) / step + 1}, (_, i) => start + (i * step));
        }
    },
}
</script>

<style scoped>

</style>