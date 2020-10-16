import StudentService from "../../services/student.service";

const getDefaultState = () => {
    return {
        questionIndex: 0,
        question_question_area: 0,
        answer_question_area: 0,
        type2Match: [],
        start_time: 0,
        isShown: false,
        isHomeworkValid: true,
        homeworkId: null,
        questionId: null,
        isDone: false,
        homework_started: false,
        answers: { data: null },
        deadline: null,
        showAnswers: false,
        screenshot: "",
        isScreenshot: 0,
        student_answer: [],
        question_count: 0,
    };
};
// initial state
const state = getDefaultState();
const actions = {
    resetHomeworkState({ commit }) {
        commit("resetState");
    },
    async callAnswers({ commit, state }) {
        await StudentService.GetAnswers(state.homeworkId).then((res) => {
            if (res) {
                commit("setAnswers", res);
                console.log(res);
            }
        });
    },

    setShowAnswers({ commit }) {
        commit("showAnswers");
    },
    callToggleScreenshot({ commit }) {
        commit("toggleScreenshot");
    },
    async setScreenshot({ commit }, data) {
        commit("setScreenshot", data);
        const question_id = state.student_answer[0].question_id;
        await StudentService.setScreenshot(state.homeworkId, question_id, {
            screenshot: data,
        }).then((res) => {
            if (res) {
                console.log("screenshot saved", res);
            }
        });
    },
    setStudentAnswer({ commit }, data) {
        commit("setStudentAnswerData", data);
    },
    setQuestionCount({ commit }, data) {
        commit("setQuestionCount", data);
    },
};
const mutations = {
    resetState(state) {
        Object.assign(state, getDefaultState());
    },
    nextQuestion(state) {
        state.questionIndex++;
    },
    prevQuestion(state) {
        state.questionIndex--;
    },
    callQuestionArea(state) {
        state.question_question_area++;
    },
    callAnswerArea(state) {
        state.answer_question_area++;
    },
    addMatching(state, match) {
        _.remove(state.type2Match, (m) => {
            return m.option === match.option;
        });
        state.type2Match.push(match);
    },
    removeMatching(state, match) {
        debugger;
        _.remove(state.type2Match, (m) => {
            return m.option === match;
        });
    },
    setTime(state) {
        state.start_time = new Date();
    },
    toggleShow(state) {
        state.isShown = !state.isShown;
    },
    toggleHomeworkValidity(state) {
        state.isHomeworkValid = !state.isHomeworkValid;
    },
    async finishHomework(state) {
        console.log("called");
        if (!state.isDone) {
            const questionResult = [
                {
                    done: 1,
                },
            ];
            console.log("called1");

            await StudentService.UpdateAllQuestionResult(
                state.homeworkId,
                state.questionId,
                questionResult
            ).then((res) => {
                console.log(res);
                console.log("called2");
            });
        }
    },
    setHomeworkId(state, id) {
        state.homeworkId = id;
    },
    setQuestionCount(state, questionCount) {
        state.question_count = questionCount;
    },
    setQuestionId(state, id) {
        state.questionId = id;
    },
    isDone(state) {
        state.isDone = true;
    },
    toggleHomeworkStarted(state) {
        state.homework_started = true;
    },
    setAnswers(state, data) {
        state.answers = data;
    },
    setDeadline(state, data) {
        state.deadline = data;
    },
    showAnswers(state) {
        state.showAnswers = true;
    },
    setScreenshot(state, data) {
        state.screenshot = data;
    },
    toggleScreenshot(state) {
        state.isScreenshot = !state.isScreenshot;
    },
    setStudentAnswerData(state, data) {
        state.student_answer = [...data];
    },
};
export default {
    namespaced: true,
    state,
    getters: {
        questionTime: (state) => {
            return state.start_time;
        },
        answers: (state) => {
            return state.answers;
        },

        showAnswers: (state) => {
            return state.showAnswers;
        },
        isScreenshot: (state) => {
            return state.isScreenshot;
        },
        getScreenshot: (state) => {
            return state.screenshot;
        },
        isDone: (state) => {
            return state.isDone;
        },
        getQuestionCount: (state) => {
            return state.question_count;
        },
    },
    actions,
    mutations,
};
