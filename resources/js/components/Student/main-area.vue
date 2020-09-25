<template>
    <div>
        <div class="canvas">
            <previous
                :jsonQuestions.sync="jsonQuestions"
                v-on:prev-question="$emit('change-question', 'prev')"
            ></previous>
            <div class="main-question-area">
                <answer-area
                    v-on:settler="$emit('settler')"
                    v-on:prev-question="$emit('prev-question')"
                    v-on:toggle-show="$emit('toggle-show')"
                    v-on:next-question="$emit('next-question')"
                    :isShown="isShown"
                    :isLoaded="isLoaded"
                    :preQuestion.sync="preQuestion"
                    :jsonQuestions.sync="jsonQuestions"
                ></answer-area>
                <question-area
                    v-on:prev-question="$emit('prev-question')"
                    v-on:toggle-show="$emit('toggle-show')"
                    v-on:next-question="$emit('next-question')"
                    :isShown="isShown"
                    :isLoaded="isLoaded"
                    :preQuestion.sync="preQuestion"
                    :jsonQuestions.sync="jsonQuestions"
                ></question-area>
            </div>
            <next
                :isLoaded="isLoaded"
                :jsonQuestions.sync="jsonQuestions"
                v-on:next-question="$emit('change-question', 'next')"
                v-on:go-back="$emit('go-back')"
                v-on:submit="$emit('submit')"
            ></next>
        </div>
    </div>
</template>
<script>
import questionArea from "./question-area";
import answerArea from "./answer-area";
import next from "./next";
import previous from "./previous";
export default {
    name: "mainArea",
    props: ["jsonQuestions", "isLoaded", "preQuestion", "isShown"],
    components: {
        questionArea,
        answerArea,
        next,
        previous,
    },
};
</script>

<style scoped>
.canvas {
    height: 574px;
    display: grid;
    grid-template-columns: 1fr 900px 1fr;
    margin: 20px auto;
}
.main-question-area {
    display: grid;
    grid-template-columns: 308px 592px;
}
</style>
