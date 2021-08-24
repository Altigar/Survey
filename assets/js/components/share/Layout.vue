<template>
    <div class="card">
        <div class="card-body">
            <h3 class="mb-2">Copy link</h3>
            <p class="mb-2">
                <a class="text-decoration-none link-primary" :href="path" target="_blank">{{ path }}</a>
            </p>
            <div class="input-group">
                <input id="link" type="text" class="form-control me-3 rounded" :value="path">
                <button class="btn btn-success rounded" type="button" @click="copy">Copy link</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Layout",
    props: {
        path: String,
    },
    methods: {
        copy() {
            let link = document.getElementById('link');
            link.select();
            link.setSelectionRange(0, 99999);
            document.execCommand('copy');
        },
    }
}
</script>

<style scoped>

</style>