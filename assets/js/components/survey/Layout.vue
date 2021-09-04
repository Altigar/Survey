<template>
    <div>
        <app-error v-if="error" @close="closeError">{{ error }}</app-error>
        <div class="card shadow-sm">
            <div class="table-responsive card-body">
                <table class="table table-striped table-hover table-borderless">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="survey in data">
                        <td v-if="survey.name.length >= 70">{{ survey.name.substr(0, 70) }}...</td>
                        <td v-else>{{ survey.name }}</td>
                        <td>{{ formatDate(survey.createdAt) }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a :href="`/content/${survey.id}`" class="dropdown-item">View</a></li>
                                    <li><a @click.prevent="remove(survey.id)" class="dropdown-item">Delete</a></li>
                                </ul>
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
            return moment(value).format('YYYY-MM-DD H:m');
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

</style>