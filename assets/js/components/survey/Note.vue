<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form method="post">
                <b-form-group>
                    <b-form-input class="mb-3" v-model="data.text" value="question"></b-form-input>
                    <p v-if="error">{{ error }}</p>
                    <b-form-select v-if="data.type === 'text'" v-model="selected" :options="options" size="sm" style="width: 4rem;"></b-form-select>
                </b-form-group>
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
    name: "Note",
    props: {
        surveyId: String,
        data: Object,
    },
    data() {
        return {
            selected: this.data.row,
            options: Array.from({length: 15}, (_, i) => i + 1),
            error: null
        }
    },
    methods: {
        async save() {
            this.error = null;
            try {
                await axios.put(`/survey/plan/${this.surveyId}/update`, {
                    id: this.data.id,
                    type: this.data.type,
                    text: this.data.text,
                    row: this.selected,
                });
            } catch (error) {
                this.error = error.response.data.text;
                this.$forceUpdate();
            }
        }
    },
}
</script>

<style scoped>

</style>