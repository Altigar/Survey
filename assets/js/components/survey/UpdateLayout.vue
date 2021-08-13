<template>
    <div>
        <div v-if="error" class="alert alert-danger" role="alert">
            <span>{{ error }}</span>
        </div>
        <div class="card">
            <div class="card-body">
                <h3>Edit survey</h3>
                <form>
                    <div class="mb-2">
                        <label for="name" class="form-label">Name</label>
                        <input v-if="data" v-model="data.name" type="text" class="form-control mb-2" id="name">
                        <v-form-error v-if="errors.name">{{ errors.name }}</v-form-error>
                    </div>
                    <div class="mb-2">
                        <label for="description" class="form-label">Description</label>
                        <textarea v-if="data" v-model="data.description" type="text" class="form-control mb-2" id="description"></textarea>
                        <v-form-error v-if="errors.description">{{ errors.description }}</v-form-error>
                    </div>
                    <div class="mb-2">
                        <v-switch v-if="data" v-model="data.repeatable" id="repeatable">Re-participate in a survey</v-switch>
                    </div>
                    <button v-if="loading" class="btn btn-primary" type="button" disabled>
                        <span class="me-2">Create</span>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                    <button v-else @click="save" type="button" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios";
import VSwitch from "../VSwitch";
import VFormError from "../VFormError";

export default {
    name: "Layout",
    components: {VSwitch, VFormError},
    props: {
        survey: String
    },
    data() {
        return {
            data: null,
            loading: false,
            error: null,
            errors: {},
        };
    },
    methods: {
        async save() {
            this.loading = true;
            this.errors = {};
            this.error = null;
            try {
                await axios.put(`/survey/edit/${this.data.id}`, {
                    name: this.data.name,
                    description: this.data.description,
                    repeatable: this.data.repeatable,
                });
            } catch (error) {
                switch (error.response.status) {
                    case 422:
                        this.errors = error.response.data;
                        break;
                    default:
                        this.error = 'Something went wrong';
                        break;
                }
            }
            this.loading = false;
        }
    },
    mounted() {
        this.data = JSON.parse(this.survey);
    }
}
</script>

<style scoped>

</style>