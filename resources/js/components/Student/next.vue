<template>
    <div v-if="this.isLoaded" class="main">
    <button v-if="this.questionIndex < (this.question.length-1) " class="button is-primary" @click="$emit('next-question')">NEXT</button>
    </div>
</template>

<script>
    export default {
        props: ["jsonQuestions","questionIndex","isLoaded"],
        name: "next",
        data (){
            return {

                question: this.jsonQuestions}}
                ,
        watch: {
            // whenever question changes, this function will run
            jsonQuestions: function () {
                this.question =this.jsonQuestions.data.questions
            }
        }
    }
</script>

<style scoped>
    .main{
        background-color: white;
        border-left: 1px solid gray ;
    }
    button{
        margin: 250px auto;
        display: block;

    }
</style>
