<template>
    <div @click="edited = true" class="card pr-0 mb-4 bg-white rounded border">
        <div class="card-body">
            <div v-if="!edited">
                <h3>{{ data.text }}</h3>
                <div class="d-flex justify-content-between mb-2">
                    <div><span>{{ textFrom }}</span></div>
                    <div><span>{{ textTo }}</span></div>
                </div>
                <div class="d-flex justify-content-around">
                    <div v-for="number in range(1, selected, 1)" :key="number" class="form-check">
                        <input class="form-check-input" type="radio">
                        <label class="form-check-label">{{ number }}</label>
                    </div>
                </div>
            </div>
            <form v-else-if="edited" method="post">
                <div class="mb-2">
                    <input v-model="data.text" type="text" class="form-control mb-2" placeholder="Question text">
                    <p v-if="error">{{ error }}</p>
                    <select v-model="selected" class="form-select form-select-sm mb-2" style="width: 5rem;">
                        <option v-for="option in options">{{ option }}</option>
                    </select>
                    <input v-model="textFrom" type="text" class="form-control mb-2" placeholder="From">
                    <p v-if="textFromError"><small>{{ textFromError }}</small></p>
                    <input v-model="textTo" type="text" class="form-control mb-2" placeholder="To">
                    <p v-if="textToError"><small>{{ textToError }}</small></p>
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
    name: "Scale",
    mixins: [Base],
    components: {VFooter, VSwitch},
    props: {
        data: Object,
    },
    data() {
        return {
            switch_id: null,
            options: this.range(2, 10, 1),
            selected: null,
            textFrom: null,
            textTo: null,
            error: null,
            textFromError: null,
            textToError: null,
        };
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
                    options: [{
                        ordering: this.data.options[0].ordering,
                        scale: Number(this.selected),
                        scale_from_text: this.textFrom,
                        scale_to_text: this.textTo,
                    }]
                });
                this.edited = false;
            } catch (error) {
                this.error = error.response.data.text;
                this.textFromError = error.response.data.scale_from_text;
                this.textToError = error.response.data.scale_to_text;
                this.$forceUpdate();
            }
        },
        range(start, stop, step) {
            return Array.from({length: (stop - start) / step + 1}, (_, i) => start + (i * step));
        }
    },
    created() {
        if (this.data.options[0]) {
            this.selected = this.data.options[0].scale;
            this.textFrom = this.data.options[0].scaleFromText;
            this.textTo = this.data.options[0].scaleToText;
        }
        this.switch_id = `switch_${this.data.id}`;
    }
}
</script>

<style scoped>

</style>