<template>
    <div class="card rounded border mb-3" @click="edited = true">
        <div class="card-body">
            <div v-if="!edited">
                <h3>{{ data.text }}</h3>
                <textarea class="form-control resize-none" :rows="selected"></textarea>
            </div>
            <form method="post" v-else-if="edited">
                <div class="mb-2">
                    <input v-model="data.text" type="text" class="form-control mb-2" placeholder="Question text">
                    <p v-if="error">{{ error }}</p>
                    <select v-model="selected" class="form-select form-select-sm mb-2" style="width: 5rem;">
                        <option v-for="option in options">{{ option }}</option>
                    </select>
                    <p v-if="rowError"><small>{{ rowError }}</small></p>
                    <v-switch :id="switch_id" v-model="data.isRequired">Required</v-switch>
                </div>
                <v-footer @save="save" @remove="$emit('remove', data.id)" @edit.stop="edited = false"></v-footer>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Base from "./Base";
import VSwitch from "../VSwitch";
import VFooter from "./VFooter";

export default {
    name: "NoteArea",
    mixins: [Base],
    components: {VFooter, VSwitch},
    props: {
        surveyId: String,
        data: Object,
    },
    data() {
        return {
            switch_id: null,
            selected: null,
            options: Array.from({length: 10}, (_, i) => i + 1),
            error: null,
            rowError: null,
        }
    },
    methods: {
        async save() {
            this.error = null;
            this.rowError = null;
            this.data.options[0].row = this.selected;
            try {
                await axios.put(`/content/${this.data.id}`, {
                    id: this.data.id,
                    type: this.data.type,
                    text: this.data.text,
                    ordering: this.data.ordering,
                    isRequired: this.data.isRequired,
                    options: [{
                        ordering: this.data.options[0].ordering,
                        row: Number(this.selected),
                    }]
                });
                this.edited = false;
            } catch (error) {
                this.error = error.response.data.text;
                this.rowError = error.response.data.row;
                this.$forceUpdate();
            }
        }
    },
    created() {
        if (this.data.options[0]) {
            this.selected = this.data.options[0].row;
        }
        this.switch_id = `switch_${this.data.id}`;
    }
}
</script>

<style scoped>

</style>