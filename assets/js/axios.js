import axios from "axios";

axios.interceptors.request.use(function (config) {
	config.headers['X-Requested-With'] = 'XMLHttpRequest';
	return config;
})

export default axios;