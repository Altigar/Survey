/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
require('bootstrap')
// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";

import Vue from "vue";
//Layouts
import ContentLayout from "./components/content/Layout";
import PassLayout from "./components/pass/Layout";
import SurveyLayout from "./components/survey/Layout";
import UpdateSurveyLayout from "./components/survey/UpdateLayout";
//Components
import AppError from "./components/AppError";
import AppFormError from "./components/AppFormError";
import AppSwitch from "./components/AppSwitch";

new Vue({
	el: '#app',
	// template: '<App/>',
	components: {
		//Layouts
		ContentLayout,
		SurveyLayout,
		UpdateSurveyLayout,
		PassLayout,
		//Components
		AppError,
		AppFormError,
		AppSwitch,
	},
});