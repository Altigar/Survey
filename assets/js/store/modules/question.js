import axios from "axios";

const PROCESSING = 'PROCESSING',
	SUCCESS = 'SUCCESS',
	FAILURE = 'FAILURE',
	SAVE = 'SAVE';

export default {
	namespaced: true,
	state: {
		error: null,
		items: []
	},
	getters: {
		error(state) {
			return state.error;
		},
		getItems(state) {
			return state.items;
		},
	},
	mutations: {
		[PROCESSING](state) {
			state.error = null;
		},
		[SUCCESS](state, payload) {
			state.items = payload;
			state.error = null;
		},
		[FAILURE](state, error) {
			state.error = error;
		},
		[SAVE](state, payload) {
			state.items = payload;
		}
	},
	actions: {
		async request({commit}, id) {
			commit(PROCESSING);
			try {
				let response = await axios.get(`/survey/plan/${id}/all`);
				commit(SUCCESS, JSON.parse(response.data));
			} catch (error) {
				commit(FAILURE, error.response.data);
			}
		},
		save({commit}, payload) {
			commit(SAVE, payload);
		},
	}
}