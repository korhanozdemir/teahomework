import Vue from "vue";
import Vuex from "vuex";
import homework from "./modules/homework";
import teacher from "./modules/teacher";

Vue.use(Vuex);

const getDefaultState = () => {
    return {
        active_tab: 0,
        user: 0,
        notification: 0,
        notification_clicked: 0,
        notificationHomeworkID: 0,
    };
};
// initial state
const state = getDefaultState();
const actions = {
    setProfileTab({ commit }, index) {
        commit("setTab", index);
    },
    setCurrentUser({ commit }, index) {
        commit("setUser", index);
    },
    setNotificationStatus({ commit }, index) {
        commit("setNotification", index);
    },
    setNotificationClicked({ commit }) {
        commit("setNotification_clicked");
    },
    setNotificationHomeworkID({ commit }, index) {
        commit("setNotificationHomework_ID", index);
    },
};
const mutations = {
    setTab(state, index) {
        state.active_tab = index;
    },
    setUser(state, index) {
        state.user = index;
    },
    setNotification(state, index) {
        state.notification = index;
    },
    setNotification_clicked(state) {
        state.notification_clicked = !state.notification_clicked;
    },
    setNotificationHomework_ID(state, index) {
        state.notificationHomeworkID = index;
    },
};
const getters = {
    getTab: (state) => {
        return state.active_tab;
    },
    getUser: (state) => {
        return state.user;
    },
    getNotificationStatus: (state) => {
        return state.notification;
    },
    getNotification_clickedStatus: (state) => {
        return state.notification_clicked;
    },
    getNotificationHomeworkID: (state) => {
        return state.notificationHomeworkID;
    },
};
export default new Vuex.Store({
    modules: {
        homework,
        teacher,
    },
    state,
    getters,
    actions,
    mutations,
});
