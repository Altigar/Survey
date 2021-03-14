<template>
    <article class="pr-0 shadow-sm mb-4 bg-white rounded border">
        <div class="card p-3 border-0">
            <b-form>
                <b-form-select v-model="selected" :options="options" size="sm"></b-form-select>
                <b-form-group v-if="isSelected('radio')">
                    <b-form-input class="mb-3" v-model="radioText"></b-form-input>
                    <div v-for="(v, index) in radioOptions" :key="index">
                        <b-form-input v-model="v.text" size="sm"></b-form-input>
                        <b-btn @click="remove(index)">remove</b-btn>
                    </div>
                </b-form-group>
                <b-form-group v-if="isSelected('checkbox')">
                    <b-form-input class="mb-3" v-model="checkboxText"></b-form-input>
                    <div v-for="(v, index) in checkboxOptions" :key="index">
                        <b-form-input v-model="v.text" size="sm"></b-form-input>
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

export default {
    name: "Question",
    data() {
        return {
            selected: 'radio',
            radioText: '',
            radioOptions: [
                {text: ''},
                {text: ''}
            ],
            checkboxText: '',
            checkboxOptions: [
                {text: ''},
                {text: ''}
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
                    this.radioOptions.push({text: ''});
                    break;
                case 'checkbox':
                    this.checkboxOptions.push({text: ''});
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
            console.log(question);
        }
    },
}
</script>

<style scoped>

</style>