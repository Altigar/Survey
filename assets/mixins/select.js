import axios from "axios";

export default {
	props: {
		surveyId: String,
		questionId: Number,
		data: Object,
		type: String,
		index: Number,
	},
	data() {
		return {
			question: {text: '', error: ''},
			options: [
				{text: '', error: '', ordering: 1},
				{text: '', error: '', ordering: 2}
			],
		};
	},
	created() {
		if (this.data) {
			this.question.text = this.data.text;
			this.options = this.data.options;
		}
	},
	computed: {
		sortedOptions() {
			return this.options.sort((a, b) => a.ordering - b.ordering);
		}
	},
	methods: {
		add() {
			let orders = [];
			for (let option of this.options) {
				orders.push(option.ordering);
			}
			this.options.push({text: '', error: '', ordering: Math.max(...orders) + 1});
		},
		remove(number) {
			this.options = this.options.filter((elem, index) => index !== number);
			let order = 1;
			for (let option of this.options) {
				option.ordering = order;
				order++;
			}
		},
		async save() {
			this.question.error = '';
			for (let option of this.options) {
				option.error = '';
			}
			let question = {
				id: this.questionId,
				type: this.type,
				text: this.question.text,
				options: this.options,
			};
			try {
				await axios.post(`/survey/plan/${this.surveyId}`, question);
			} catch (error) {
				let data = error.response.data;
				for (let key in data) {
					if (!data.hasOwnProperty(key)) {
						continue;
					}
					if (typeof data[key] === 'object') {
						let nestedData = data[key];
						for (let nestedKey in nestedData) {
							if (!nestedData.hasOwnProperty(nestedKey)) {
								continue;
							}
							this.options[nestedKey].error = data[key][nestedKey].text;
						}
					} else {
						this.question.error = data.text;
					}
				}
				this.$forceUpdate();
			}
		}
	}
}
