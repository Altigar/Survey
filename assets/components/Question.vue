<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form method="post">
                <b-form-select v-model="selected" :options="options" size="sm"></b-form-select>
                <b-form-group v-if="isSelected('radio')">
                    <b-form-input class="mb-3" v-model="radioText"></b-form-input>
                    <div v-for="(option, index) in radioOptions" :key="index">
                        <b-form-input v-model="option.text" size="sm"></b-form-input>
                        <p v-if="option.error">{{ option.error }}</p>
                        <b-btn @click="remove(index)">remove</b-btn>
                    </div>
                </b-form-group>
                <b-form-group v-else-if="isSelected('checkbox')">
                    <b-form-input class="mb-3" v-model="checkboxText"></b-form-input>
                    <div v-for="(option, index) in checkboxOptions" :key="index">
                        <b-form-input v-model="option.text" size="sm"></b-form-input>
                        <p v-if="option.error">{{ option.error }}</p>
                        <b-btn @click="remove(index)">remove</b-btn>
                    </div>
                </b-form-group>
                <div>
                    <b-btn @click="add">add</b-btn>
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
            radioText: '',
            radioOptions: [
                {text: '', error: ''},
                {text: '', error: ''}
            ],
            checkboxText: '',
            checkboxOptions: [
                {text: '', error: ''},
                {text: '', error: ''}
            ],
            options: [
                {value: 'radio', text: 'radio'},
                {value: 'checkbox', text: 'checkbox'}
            ],
        };
    },
    methods: {
        add() {
            switch (this.selected) {
                case 'radio':
                    this.radioOptions.push({text: '', error: ''});
                    break;
                case 'checkbox':
                    this.checkboxOptions.push({text: '', error: ''});
                    break;
            }
        },
        remove(number) {
            switch (this.selected) {
                case 'radio':
                    this.radioOptions = this.radioOptions.filter((elem, index) => index !== number);
                    break;
                case 'checkbox':
                    this.checkboxOptions = this.checkboxOptions.filter((elem, index) => index !== number);
                    break;
            }
        },
        isSelected(type) {
            return this.selected === type;
        },
        clearErrors() {
            let options = [];
            switch (this.selected) {
                case 'radio':
                    options = this.radioOptions;
                    break;
                case 'checkbox':
                    options = this.checkboxOptions;
                    break;
            }
            for (let option of options) {
                option.error = '';
            }
        },
        async save() {
            let question = {};
            switch (this.selected) {
                case 'radio':
                    question = {text: this.radioText, options: this.radioOptions, type: this.selected};
                    break;
                case 'checkbox':
                    question = {text: this.checkboxText, options: this.checkboxOptions, type: this.selected};
                    break;
            }
            this.clearErrors();
            try {
                await axios.post(`/survey/plan/${this.id}`, question);
            } catch (error) {
                let data = error.response.data;
                for (let value in data) {
                    if (data.hasOwnProperty(value)) {
                        let matches = value.match(/options\[(?<index>\d+)\]/);
                        if (matches && Object.prototype.hasOwnProperty.call(matches.groups, 'index')) {
                            let i = matches.groups.index;
                            if (this.selected === 'radio') {
                                this.radioOptions[i].error = data[value];
                            } else  if (this.selected === 'checkbox') {
                                this.checkboxOptions[i].error = data[value];
                            }
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