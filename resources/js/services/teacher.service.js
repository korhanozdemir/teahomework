import ApiService from "./api.service";

let resource = "api/teacher/";

export default {
    //Get Data Örneği
    getAllHomeworks(params) {
        return params
            ? ApiService.get(resource + "homeworks" + "?" + params)
            : ApiService.get(resource + "homeworks");
    },
    getHomeworkClasses(homework_id) {
        return ApiService.get(resource + "class/" + homework_id);
    },
    getHomeworkClassStudents(homework_id, class_id) {
        return ApiService.get(
            resource + "student/" + homework_id + "/" + class_id
        );
    },
    postComment(id, comment) {
        return ApiService.post(resource + "homeworks/comment/" + id, {
            teacher_comment: comment,
        });
    },
    getStudentAnswers(homework_id, student_id, image) {
        if (image) {
            return ApiService.get(
                resource +
                    "homework-student-results/" +
                    homework_id +
                    "/" +
                    student_id +
                    "/" +
                    image
            );
        } else {
            return ApiService.get(
                resource +
                    "homework-student-results/" +
                    homework_id +
                    "/" +
                    student_id
            );
        }
    },
    rateQuestion(answer_id, rate) {
        return ApiService.post(resource + "homeworks/rate/" + answer_id, {
            teacher_rate: rate,
        });
    },
    deleteHomework(homework_id) {
        return ApiService.delete(resource + "homeworks/" + homework_id);
    },
    changeDeadline(homework_id, deadline) {
        return ApiService.put(resource + "homeworks/" + homework_id, {
            deadline: deadline,
        });
    },
    getTab2Classes() {
        return ApiService.get(resource + "table2");
    },
    getTab2Homeworks(class_id) {
        return ApiService.get(resource + "table2/" + class_id);
    },
    /*Manager services*/
    getCourses() {
        return ApiService.get(resource + "table-manager");
    },
    getTeachers(course_id) {
        return ApiService.get(resource + "table-manager/" + course_id);
    },
    getHomeworkCoursesTeacher(course_id, teacher_id) {
        return ApiService.get(
            resource + "table-manager/" + course_id + "/" + teacher_id
        );
    },
    getManagerClasses(class_id, course_id) {
        if (course_id) {
            return ApiService.get(
                resource + "class-manager/" + class_id + "/" + course_id
            );
        } else if (class_id) {
            return ApiService.get(resource + "class-manager/" + class_id);
        } else {
            return ApiService.get(resource + "class-manager");
        }
    },
};
