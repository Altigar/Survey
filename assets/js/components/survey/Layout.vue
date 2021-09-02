<template>
    <div>
        <app-error v-if="error" @close="closeError">{{ error }}</app-error>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Surveys</h2>
                    <a :href="'/survey/create'" class="btn btn-success">Create survey</a>
                </div>
            </div>
            <div class="table-responsive card-body">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="survey in data">
                        <td v-if="survey.name.length >= 70">{{ survey.name.substr(0, 70) }}...</td>
                        <td v-else>{{ survey.name }}</td>
                        <td>{{ formatDate(survey.createdAt) }}</td>
                        <td>
                            <div class="d-flex">
                                <a :href="`/content/${survey.id}`" class="btn btn-info text-light me-1">View</a>
                                <a @click.prevent="remove(survey.id)" class="btn btn-danger text-light me-1">Delete</a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios";
import moment from "moment";
import AppError from "../AppError";

export default {
    name: "Layout",
    components: {AppError},
    props: {
        surveys: String,
    },
    data() {
        return {
            data: null,
            error: null,
        }
    },
    methods: {
        async remove(id) {
            if (!confirm('Are you sure you want to delete this survey? All data related to this survey will be deleted')) {
                return;
            }
            try {
                await axios.delete(`/survey/${id}`);
                let response = await axios.get('/survey');
                this.data = response.data;
            } catch (error) {
                this.error = 'Failed to delete survey';
            }
        },
        formatDate(value) {
            return moment(value).format('YYYY-MM-DD');
        },
        closeError() {
            this.error = null;
        },
    },
    mounted() {
        this.data = JSON.parse(this.surveys);
    }
}
</script>

<style scoped>
    table tr:last-child td {
        border-style: none !important;
    }
</style>