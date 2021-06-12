<template>
    <aside>
        <b-list-group>
            <b-list-group-item v-for="item in data" @click="add(item)" :key="item.value" button>{{ item.value }}</b-list-group-item>
        </b-list-group>
    </aside>
</template>

<script>
export default {
    name: "Sidebar",
    props: {
        data: Array,
    },
    methods: {
        add(item) {
            this.$emit('add', item);
        }
    }
}
</script>

<style scoped>

</style>