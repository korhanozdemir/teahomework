import ApiService from "./api.service";

let resource = "api/student/";

export default {
    //Get Data Örneği
    getStudents() {
        return ApiService.get(resource + "get-students");
    },
    getNotifications() {
        return ApiService.get(resource + "get-notifications");
    },
    setNotifications() {
        return ApiService.get(resource + "set-notifications");
    },
    getStudentDetails() {
        return ApiService.get(resource + "get-student-details");
    },

    //Post Sırasında Data Göndermek için kullanılan örnek
    PostCheckOut(students, data) {
        return ApiService.post(resource + "session-attendance", {
            users: students,
            data: data,
        });
    },

    UpdateAllQuestionResult(homework_id, question_id, data) {
        return ApiService.post(
            resource +
                "homework-student-results/" +
                homework_id +
                "/" +
                question_id,
            data
        );
    },

    GetAllQuestionResult(homework_id, question_id, data) {
        return ApiService.get(
            resource +
                "homework-student-results/" +
                homework_id +
                "/" +
                question_id
        );
    },
    GetHomework(homework_id) {
        if (homework_id) {
            return ApiService.get(resource + "get-homework/" + homework_id);
        } else {
            return ApiService.get(resource + "get-homework/");
        }
    },
    GetAnswers(homework_id) {
        return ApiService.get(resource + "homework-answers/" + homework_id);
    },
    GetProfileInfo() {
        return ApiService.get("api/user-info/");
    },
    setScreenshot(homework_id, question_id, data) {
        return ApiService.post(
            resource +
                "homework-student-result-screenshot/" +
                homework_id +
                "/" +
                question_id,
            data
        );
    },
};
