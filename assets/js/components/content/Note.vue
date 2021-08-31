<template>
    <div @click="edited = true" class="card">
        <div class="card-body">
            <div v-if="!edited">
                <h3>{{ data.text }}</h3>
                <input type="text" class="form-control">
            </div>
            <form method="post" v-else-if="edited">
                <div class="mb-2">
                    <input v-model="data.text" type="text" class="form-control mb-2" placeholder="Question text">
                    <app-form-error v-if="error">{{ error }}</app-form-error>
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
    name: "Note",
    mixins: [Base],
    components: {AppFormError, VFooter, AppSwitch},
    props: {
        surveyId: String,
        data: Object,
    },
    data() {
        return {
            selected: null,
            error: null,
            options: Array.from({length: 10}, (_, i) => i + 1),
        }
    },
    computed: {
        switchId() {
            return `switch_${this.data.id}`;
        }
    },
    methods: {
        async save() {
            this.error = null;
            try {
                await axios.put(`/content/${this.data.id}`, this.data);
                this.edited = false;
            } catch (error) {
                if (error.response.status === 422) {
                    this.error = error.response.data.text;
                } else {
                    this.$emit('showError', 'Something went wrong');
                }
                this.$forceUpdate();
            }
        }
    },
}
</script>

<style scoped>

</style>