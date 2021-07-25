<template>
    <div class="card rounded border mb-3" @click="edited = true">
        <div class="card-body">
            <div v-if="!edited">
                <h3>{{ data.text }}</h3>
                <input type="text" class="form-control">
            </div>
            <form method="post" v-else-if="edited">
                <div class="mb-2">
                    <input v-model="data.text" type="text" class="form-control mb-2" placeholder="Question text">
                    <p v-if="error">{{ error }}</p>
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
    name: "Note",
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
            error: null,
            options: Array.from({length: 10}, (_, i) => i + 1),
        }
    },
    methods: {
        async save() {
            this.error = null;
            try {
                await axios.put(`/content/${this.data.id}`, this.data);
                this.edited = false;
            } catch (error) {
                this.error = error.response.data.text;
                this.$forceUpdate();
            }
        }
    },
    created() {
        this.switch_id = `switch_${this.data.id}`;
    }
}
</script>

<style scoped>

</style>