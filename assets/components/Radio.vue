<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form method="post">
                <b-form-group>
                    <b-form-input class="mb-3" v-model="value.text"></b-form-input>
                    <p v-if="value.error">{{ value.error }}</p>
                    <div v-for="(option, index) in sortedOptions" :key="index">
                        <b-form-input v-model="option.text" size="sm"></b-form-input>
                        <p v-if="option.error">{{ option.error }}</p>
                        <b-btn @click="remove(index)">remove</b-btn>
                    </div>
                </b-form-group>
                <div>
                    <b-btn @click="add">add</b-btn>
                    <b-btn @click="save">save</b-btn>
                    <b-btn v-if="id" @click="$emit('purge', id)">purge</b-btn>
                    <b-btn v-else @click="$emit('remove', index)">remove</b-btn>
                </div>
            </b-form>
        </div>
    </article>
</template>

<script>
import select from "../mixins/select";

export default {
    name: "Radio",
    mixins: [select],
}
</script>

<style scoped>

</style>