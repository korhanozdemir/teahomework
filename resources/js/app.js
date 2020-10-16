/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

import Tooltip from "vue-directive-tooltip";
import "vue-directive-tooltip/dist/vueDirectiveTooltip.css";
window.Vue = require("vue");

import store from "./store/Index";
import Vue from "vue";
import VuePlyr from "vue-plyr";
import VueGoodTablePlugin from "vue-good-table";
import "vue-good-table/dist/vue-good-table.css";
import Datetime from "vue-datetime";
// You need a specific loader for CSS files
import "vue-datetime/dist/vue-datetime.css";

Vue.use(Datetime);
Vue.use(VueGoodTablePlugin);
Vue.use(VuePlyr, {
    plyr: {
        fullscreen: { enabled: false },
    },
    emit: ["ended"],
});
Vue.use(Tooltip);
Vue.config.productionTip = false;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component("admin-page", require("./components/Pages/AdminPage").default);
Vue.component(
    "teacher-page",
    require("./components/Pages/TeacherPage").default
);
Vue.component(
    "student-page",
    require("./components/Pages/StudentPage").default
);

// Student Components
//Vue.component('student-page', require('./components/Pages/StudentPage').default);
Vue.component("home", require("./components/Student/Home").default);

Vue.component(
    "test-calendar",
    require("./components/Student/test-calendar").default
);
Vue.component("student", require("./components/Student/student").default);
Vue.component("profile-menu", require("./components/profile-menu").default);

// Teacher Components

Vue.component("teacher", require("./components/Teacher/teacher").default);

// Manager
Vue.component("manager", require("./components/Manager/manager").default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    store: store,
    el: "#app",
});
