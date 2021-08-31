<template>
    <div @click="edited = true" class="card">
        <div class="card-body">
            <div v-if="!edited">
                <h3>{{ data.text }}</h3>
                <textarea class="form-control resize-none" :rows="data.row"></textarea>
            </div>
            <form method="post" v-else-if="edited">
                <div class="mb-2">
                    <div class="mb-2">
                        <input v-model="data.text" type="text" class="form-control mb-2" placeholder="Question text">
                        <app-form-error v-if="error">{{ error }}</app-form-error>
                    </div>
                    <div class="mb-2">
                        <select v-model="data.row" class="form-select form-select-sm mb-2" style="width: 5rem;">
                            <option v-for="option in options">{{ option }}</option>
                        </select>
                        <app-form-error v-if="rowError">{{ rowError }}</app-form-error>
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
    name: "NoteArea",
    mixins: [Base],
    components: {AppFormError, VFooter, AppSwitch},
    props: {
        surveyId: String,
        data: Object,
    },
    data() {
        return {
            switch_id: null,
            options: Array.from({length: 10}, (_, i) => i + 1),
            error: null,
            rowError: null,
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
            this.rowError = null;
            try {
                await axios.put(`/content/${this.data.id}`, {
                    id: this.data.id,
                    type: this.data.type,
                    text: this.data.text,
                    ordering: this.data.ordering,
                    isRequired: this.data.isRequired,
                    row: Number(this.data.row),
                });
                this.edited = false;
            } catch (error) {
                this.error = error.response.data.text;
                this.rowError = error.response.data.row;
                this.$forceUpdate();
            }
        }
    },
}
</script>

<style scoped>

</style>