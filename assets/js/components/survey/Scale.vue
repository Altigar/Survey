<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form method="post">
                <b-form-input class="mb-3" v-model="data.text" value="question"></b-form-input>
                <p v-if="error">{{ error }}</p>
                <b-form-select class="mb-3" v-model="selected" :options="options" size="sm" style="width: 4rem;"></b-form-select>
                <b-form-input class="mb-3" v-model="textFrom" placeholder="From"></b-form-input>
                <b-form-input class="mb-3" v-model="textTo" placeholder="To"></b-form-input>
                <div>
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
    name: "Scale",
    props: {
        data: Object,
    },
    data() {
        return {
            options: this.range(2, 10, 1),
            selected: null,
            textFrom: null,
            textTo: null,
            error: null,
        };
    },
    methods: {
        async save() {
            this.error = null;
            try {
                await axios.put(`/content/${this.surveyId}`, {
                    id: this.data.id,
                    type: this.data.type,
                    text: this.data.text,
                    options: [{
                        scale: this.selected,
                        scale_from_text: this.textFrom,
                        scale_to_text: this.textTo,
                    }]
                });
            } catch (error) {
                this.error = error.response.data.text;
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
    }
}
</script>

<style scoped>

</style>