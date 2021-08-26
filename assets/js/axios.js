import axios from "axios";

axios.interceptors.request.use(config => {
	config.headers['X-CSRF-TOKEN'] = document.head.querySelector('meta[name=csrf-token]').content ?? null;
	config.headers['X-Requested-With'] = 'XMLHttpRequest';
	return config;
})

export default axios;