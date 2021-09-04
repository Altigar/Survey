<template>
    <aside class="shadow-sm">
        <ul class="list-group">
            <li class="list-group-item list-group-item-action pointer" v-for="item in data" @click="add(item)" :key="item.value">
                {{ item.value }}
            </li>
        </ul>
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