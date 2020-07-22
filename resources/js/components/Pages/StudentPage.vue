<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Student Page</div>
                    <div class="card-body">
                        Solve the Quiz
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import StudentService from '../../services/student.service';

    export default {
        mounted() {
            console.log('Component mounted.')

        },
        created() {
            StudentService.getStudentDetails()
            .then((res)=>{
                toastr.success(res.data.name, 'Hoşgeldin!');
            });

            var questionResult = [
                {
                    option: 3,
                    option_matched: true,
                    time : 2
                },
                {
                    option: 2,
                    option_matched: true,
                    time : 2
                }
            ];


           StudentService.GetAllQuestionResult(2,4)
                .then((res)=>{
                    console.log(res);
                });
            StudentService.GetHomework(2)
                .then((res)=>{
                    console.log(res);

                });
            StudentService.UpdateAllQuestionResult(2, questionResult)
                .then((res)=>{
                    console.log(res);
                })

        }
    }
</script>

<style>

</style>
