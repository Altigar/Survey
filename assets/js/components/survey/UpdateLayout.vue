<template>
    <div>
        <app-success v-if="successMessage">{{ successMessage }}</app-success>
        <div v-if="errorMessage" class="alert alert-danger" role="alert">{{ errorMessage }}</div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h3>Edit survey</h3>
                <form>
                    <div class="mb-2">
                        <label for="name" class="form-label">Name</label>
                        <input v-if="data" v-model="data.name" type="text" class="form-control mb-2" id="name">
                        <app-form-error v-if="errors.name">{{ errors.name }}</app-form-error>
                    </div>
                    <div class="mb-2">
                        <label for="description" class="form-label">Description</label>
                        <textarea v-if="data" v-model="data.description" type="text" class="form-control mb-2" id="description"></textarea>
                        <app-form-error v-if="errors.description">{{ errors.description }}</app-form-error>
                    </div>
                    <div class="mb-2">
                        <app-switch v-if="data" v-model="data.repeatable" id="repeatable">Re-participate in a survey</app-switch>
                    </div>
                    <button v-if="loading" class="btn btn-primary" type="button" disabled>
                        <span class="me-2">Update</span>
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
import AppSwitch from "../AppSwitch";
import AppFormError from "../AppFormError";
import AppSuccess from "../AppSuccess";

export default {
    name: "Layout",
    components: {AppSuccess, AppSwitch, AppFormError},
    props: {
        survey: String
    },
    data() {
        return {
            data: null,
            loading: false,
            successMessage: null,
            errorMessage: null,
            errors: {},
        };
    },
    methods: {
        async save() {
            this.loading = true;
            this.successMessage = null;
            this.errors = {};
            this.errorMessage = null;
            try {
                await axios.put(`/survey/edit/${this.data.id}`, {
                    name: this.data.name,
                    description: this.data.description,
                    repeatable: this.data.repeatable,
                });
                this.successMessage = 'Survey updated successfully';
            } catch (error) {
                switch (error.response.status) {
                    case 422:
                        this.errors = error.response.data;
                        break;
                    default:
                        this.errorMessage = 'Something went wrong';
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