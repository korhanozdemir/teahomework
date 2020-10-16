import TeacherService from "../../services/teacher.service";

const getDefaultState = () => {
    return {};
};
// initial state
const state = getDefaultState();
const actions = {
    resetTeacherState({ commit }) {
        commit("resetState");
    },
};
const mutations = {
    resetState(state) {
        Object.assign(state, getDefaultState());
    },
};
const getters = {};
export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};
