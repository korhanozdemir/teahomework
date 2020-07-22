import ApiService from './api.service';

let resource = 'student/';

export default {

    //Get Data Örneği
    getStudents(){
        return ApiService.get(resource+ "get-students" );
    },

    getStudentDetails(){
        return ApiService.get(resource+ "get-student-details" );
    },


    //Post Sırasında Data Göndermek için kullanılan örnek
    PostCheckOut(students,data){
        return ApiService.post(resource+ "session-attendance",{
            users: students,
            data: data
        })
    },

    UpdateAllQuestionResult(homework_id, question_id, data){
        return ApiService.post(resource+ "homework-student-results/"+homework_id+"/"+question_id, data)
    },

    GetAllQuestionResult(homework_id, question_id, data){
        return ApiService.get(resource+ "homework-student-results/"+homework_id+"/"+question_id)
    },
    GetHomework(homework_id){
        return ApiService.get(resource+ "get-homework/"+homework_id)
    },





}
