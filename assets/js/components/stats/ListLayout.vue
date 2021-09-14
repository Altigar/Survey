<template>
    <div>
        <app-error v-if="error" @close="closeError">{{ error }}</app-error>
        <template v-if="data.length > 0">
            <a v-for="pass in data" :key="pass.id" :href="`/stats/pass/${pass.id}`" class="text-decoration-none text-body">
                <div class="card mb-2 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div>
                                    <strong>Pass: #{{ pass.id }}</strong>
                                </div>
                                <div>
                                    <small>
                                        <time>
                                            {{ formatDate(pass.createdAt) }}
                                        </time>
                                    </small>
                                </div>
                            </div>
                            <div>
                                <a @click.prevent="remove(pass.id)" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </template>
        <div v-else class="card mb-3 shadow-sm">
            <div class="card-body">
                No results
            </div>
        </div>
    </div>
</template>

<script>
import axios from "../../axios";
import moment from "moment";
import AppError from "../AppError";

export default {
    name: "ListLayout",
    components: {AppError},
    props: {
        passes: String,
        surveyId: String,
    },
    data() {
        return {
            data: [],
            error: null,
        }
    },
    methods: {
        async remove(id) {
            if (!confirm('Are you sure you want to delete this pass? All data related to this pass will be deleted')) {
                return;
            }
            this.error = null;
            try {
                await axios.delete(`/stats/pass/${id}`);
            } catch (error) {
                this.error = 'Failed to delete pass';
                return;
            }
            try {
                if (!this.surveyId) {
                    throw new Error()
                }
                let response = await axios.get(`/stats/${this.surveyId}/pass/list`);
                this.data = response.data;
            } catch (error) {
                this.error = 'Failed to update list';
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
        this.data = JSON.parse(this.passes);
    }
}
</script>

<style scoped>

</style>