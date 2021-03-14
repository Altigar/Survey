<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form method="post">
                <b-form-select v-model="selected" :options="options" size="sm"></b-form-select>
                <b-form-group>
                    <b-form-input class="mb-3" v-model="textOptions[selected].text"></b-form-input>
                    <p v-if="textOptions[selected].error">{{ textOptions[selected].error }}</p>
                    <div v-for="(option, index) in formOptions[selected]" :key="index">
                        <b-form-input v-model="option.text" size="sm"></b-form-input>
                        <p v-if="option.error">{{ option.error }}</p>
                        <b-btn @click="remove(index)" v-if="isSelected(['radio', 'checkbox'])">remove</b-btn>
                    </div>
                </b-form-group>
                <div>
                    <b-btn @click="add" v-if="isSelected(['radio', 'checkbox'])">add</b-btn>
                    <b-btn @click="save">save</b-btn>
                </div>
            </b-form>
        </div>
    </article>
</template>

<script>
import axios from "axios";

export default {
    name: "Question",
    props: {
        id: String,
    },
    data() {
        return {
            selected: 'radio',
            textOptions: {
                radio: {text: '', error: ''},
                checkbox: {text: '', error: ''},
                string: {text: '', error: ''},
            },
            formOptions: {
                radio: [
                    {text: '', error: ''},
                    {text: '', error: ''}
                ],
                checkbox: [
                    {text: '', error: ''},
                    {text: '', error: ''}
                ],
                string: [
                    {text: '', error: ''},
                ],
            },
            options: [
                {value: 'radio', text: 'radio'},
                {value: 'checkbox', text: 'checkbox'},
                {value: 'string', text: 'string'}
            ],
        };
    },
    methods: {
        add() {
            this.formOptions[this.selected].push({text: '', error: ''});
        },
        remove(number) {
            this.formOptions[this.selected] = this.formOptions[this.selected].filter((elem, index) => index !== number);
        },
        isSelected(types) {
            return types.includes(this.selected);
        },
        async save() {
            this.textOptions[this.selected].error = '';
            for (let option of this.formOptions[this.selected]) {
                option.error = '';
            }
            let question = {text: this.textOptions[this.selected].text, options: this.formOptions[this.selected], type: this.selected};
            try {
                await axios.post(`/survey/plan/${this.id}`, question);
            } catch (error) {
                let data = error.response.data;
                for (let value in data) {
                    if (data.hasOwnProperty(value)) {
                        let matches = value.match(/options\[(?<index>\d+)\]/);
                        if (matches && Object.prototype.hasOwnProperty.call(matches.groups, 'index')) {
                            let i = matches.groups.index;
                            this.formOptions[this.selected][i].error = data[value];
                        }
                        if (value === 'text') {
                            this.textOptions[this.selected].error = data[value];
                        }
                    }
                }
            }
        }
    },
}
</script>

<style scoped>

</style>