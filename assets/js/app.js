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

import Vue from "vue";
//Layouts
import ContentLayout from "./components/content/Layout";
import PassLayout from "./components/pass/Layout";
import SurveyLayout from "./components/survey/Layout";
import UpdateSurveyLayout from "./components/survey/UpdateLayout";
import ShareLayout from "./components/share/Layout";
//Components
import AppError from "./components/AppError";
import AppFormError from "./components/AppFormError";
import AppSwitch from "./components/AppSwitch";

const layouts = {
	ContentLayout,
	SurveyLayout,
	UpdateSurveyLayout,
	PassLayout,
	ShareLayout,
};

const components = {
	AppError,
	AppFormError,
	AppSwitch,
};

new Vue({
	el: '#app',
	// template: '<App/>',
	components: {
		...layouts,
		...components
	},
});