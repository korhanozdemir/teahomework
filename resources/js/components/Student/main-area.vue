<template>
    <div >
        <div class="canvas" :class="{'small-grid':false}" >

            <previous  :questionIndex="questionIndex" :jsonQuestions.sync="jsonQuestions" v-on:prev-question="$emit('prev-question')"></previous>
            <answer-area  :questionIndex="questionIndex" :isLoaded="isLoaded" :preQuestion.sync="preQuestion" :jsonQuestions.sync="jsonQuestions"></answer-area>
            <question-area   :questionIndex="questionIndex" :isLoaded="isLoaded" :preQuestion.sync="preQuestion" :jsonQuestions.sync="jsonQuestions"></question-area>
            <next  :questionIndex="questionIndex" :isLoaded="isLoaded"  :jsonQuestions.sync="jsonQuestions" v-on:next-question="$emit('next-question')" ></next>
        </div>

    </div>
</template>

<script>
    import questionArea from "./question-area";
    import answerArea from "./answer-area"
    import next from "./next";
    import previous from "./previous";
    export default {
        name: "mainArea",
        props: ["jsonQuestions","questionIndex","isLoaded","preQuestion"],
        components:{
            questionArea,
            answerArea,
            next,
            previous
        }


    }
</script>

<style scoped>
    .canvas{
        height: 574px;
        display: grid;
        grid-template-columns: 1fr 308px 592px 1fr;
        background-color: #f2f2f2;
        margin: 20px auto ;


    }
    .small-grid{
        grid-template-columns: 1fr 592px 1fr;
    }

</style>
