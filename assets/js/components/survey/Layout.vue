<template>
    <div>
        <div v-if="error" class="alert alert-danger" role="alert">
            <span>{{ error }}</span>
        </div>
        <div class="card">
            <div class="card-body">
                <h3>Create survey</h3>
                <form>
                    <div class="mb-2">
                        <label for="name" class="form-label">Name</label>
                        <input v-model="name" type="text" class="form-control mb-2" id="name">
                        <app-form-error v-if="errors.name">{{ errors.name }}</app-form-error>
                    </div>
                    <div class="mb-2">
                        <label for="description" class="form-label">Description</label>
                        <textarea v-model="description" type="text" class="form-control mb-2" id="description"></textarea>
                        <app-form-error v-if="errors.description">{{ errors.description }}</app-form-error>
                    </div>
                    <div class="mb-2">
                        <v-switch v-model="repeatable" id="repeatable">Re-participate in a survey</v-switch>
                    </div>
                    <button v-if="loading" class="btn btn-primary" type="button" disabled>
                        <span class="me-2">Create</span>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                    <button v-else @click="save" type="button" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios";
import VSwitch from "../VSwitch";
import AppFormError from "../AppFormError";

export default {
    name: "Layout",
    components: {VSwitch, AppFormError},
    data() {
        return {
            name: '',
            description: null,
            repeatable: false,
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
                let response = await axios.post(`/survey/create`, {
                    name: this.name,
                    description: this.description,
                    repeatable: this.repeatable,
                });
                window.location.href = `/content/${response.data.id}`;
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
    }
}
</script>

<style scoped>

</style>