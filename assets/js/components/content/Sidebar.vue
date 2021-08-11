<template>
    <aside>
        <ul class="list-group">
            <li class="list-group-item list-group-item-action pointer mb-1 border rounded" v-for="item in data" @click="add(item)" :key="item.value" button>
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