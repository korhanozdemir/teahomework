<template>
    <div class="outer">
    <div v-if="isLoaded" class="answer " >
        <ul v-if="question[questionIndex].question_type === 1|| question[questionIndex].question_type === 2 " class="answers ">
            <li  class="container " :key = "option.id"   v-for= " option in question[questionIndex].options" >
                <div class="answer_image dragarea" :id="'option'+option.id"><div class="centered"><strong>{{option.option_text}}</strong></div></div>
            </li></ul>
        <ul v-if="question[questionIndex].question_type === 0 " class="answers ">
            <li  class="container" :key = "'choice' + index"  v-for= " index in question[questionIndex].option_count" >
                <div class="choice" :id="'index'+Number(index-1)" :style='{backgroundImage:"url("+"images/liconlar/option" + choices[index-1] +".png"+")"}' @click="choose(index)"></div>
            </li></ul>
        <div class="answers_text" v-if="question[questionIndex].question_type === 5  ">
            <textarea  placeholder="Cevabını buraya yaz." class="text"></textarea>
        </div>

    </div>
    <div v-else class="loading centered"></div></div>
</template>

<script>
    import StudentService from "../../services/student.service";

    export default {
        name: "answerArea",
        props: ["jsonQuestions","questionIndex","isLoaded","preQuestion"],
        data (){
            return {
                question: this.jsonQuestions,
                choices:["_a","_b","_c","_d","_e"]
            }
        },watch: {
            // whenever question changes, this function will run
            jsonQuestions: function () {
                this.question =this.jsonQuestions.data.questions
            },
            questionIndex: function () {
                this.getData(this.questionIndex)
            },
            preQuestion: function () {
                this.setData(this.questionIndex)
            }


        },
        methods:{
            choose(index){
                for(var i = 0; i<this.choices.length ;i++){

                if(this.choices[i].includes("_correct")){
                    this.choices[i] = this.choices[i].replace("_correct", "")
                }
                if(i === (index-1) ){
                        document.querySelectorAll(".choice")[i].classList.add("chosen")
                        this.choices[i] += "_correct"
                    }
                }
                for(var j = 0; j< document.querySelectorAll(".choice").length ;j++){
                document.querySelectorAll(".choice")[j].style.backgroundImage = 'url("/images/liconlar/option' + this.choices[j] +'.png")'
                    if(!(j === (index-1)) ){
                        document.querySelectorAll(".choice")[j].classList.remove("chosen")
                }}
            }   ,
            setData(question_id){
                let questionResult = []
                if(this.question[this.questionIndex].question_type === 0){
                    debugger
                    questionResult.push({answer:document.querySelector(".chosen").getAttribute("id").replace("index","")})
                }
                StudentService.UpdateAllQuestionResult(2,question_id, questionResult)
                    .then((res)=>{
                        console.log("done");
                    })
            },
            getData(i){
                StudentService.GetAllQuestionResult(2,i+1)
                    .then((res)=>{
                        this.answers = res.data;
                        if(this.answers.length>0){
                            this.useData()
                        }
                    });

            },
            useData(){
                if(this.answers[0].type === 0){
                    this.choose(Number(this.answers[0].answer)+1)
                    document.querySelectorAll(".choice")[Number(this.answers[0].answer)].classList.add("chosen")

                }
            }

        }

    }
</script>

<style scoped>
    .outer{
        position:relative;
        border: 1px solid gray ;

    }
    .choice{
        height: 60px;
        width: 60px;
        margin: auto;
        cursor: pointer;
        background-repeat:  no-repeat;
    }
    .container{
        padding-left: 9px;
        margin-top: 10px;
        z-index: 5;
        cursor: grab;
    }
    ul{
        padding-top: 20px;
    }
    .answer_image{
        background:  url('../../../assets/liconlar/matching_option_image.png') no-repeat;
        height: 47px;
        width: 281px;
    }
.centered {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
    .answers_text{
        padding: 20px;
    }
    .text{
        height: 530px;
        width: 265px;
        border-radius: 10px;
        resize: none;
        padding: 20px;
    }

</style>
