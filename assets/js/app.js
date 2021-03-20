/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";

import Vue from "vue";
import BootstrapVue from "bootstrap-vue";

import Survey from "../components/Survey";

Vue.use(BootstrapVue);

new Vue({
	el: '#app',
	// template: '<App/>',
	components: {
		Survey,
	}
});