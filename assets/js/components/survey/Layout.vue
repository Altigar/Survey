<template>
    <div>
        <app-error v-if="error" @close="closeError">{{ error }}</app-error>
        <template v-if="data.length > 0">
            <a v-for="survey in data" :key="survey.id" :href="`/content/${survey.id}`" class="text-decoration-none text-body">
                <div class="card mb-2 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div>
                                    <span v-if="survey.name.length >= 70">
                                        <strong>{{ survey.name.substr(0, 70) }}</strong>...
                                    </span>
                                    <span v-else>
                                        <strong>{{ survey.name }}</strong>
                                    </span>
                                </div>
                                <div>
                                    <small>
                                        <time>
                                            {{ formatDate(survey.createdAt) }}
                                        </time>
                                    </small>
                                </div>
                            </div>
                            <div>
                                <a @click.prevent="remove(survey.id)" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </template>
        <div v-else class="card mb-3 shadow-sm">
            <div class="card-body">
                No surveys
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
            data: [],
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