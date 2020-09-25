<template>
    <div v-if="this.isLoaded" class="main">
        <button
            :disabled="!this.$store.state.homework.isShown"
            v-if="
                this.$store.state.homework.questionIndex <
                this.question.length - 1
            "
            class="button is-primary"
            @click="$emit('next-question')"
        >
            SONRAKİ
        </button>
        <button
            :disabled="!this.$store.state.homework.isShown"
            v-if="
                !(
                    this.$store.state.homework.questionIndex <
                    this.question.length - 1
                )
            "
            class="button submit is-success"
            @click="$emit('submit')"
        >
            <span v-if="this.$store.state.homework.isDone">ÇIK</span>
            <span v-else>GÖNDER VE ÇIK</span>
        </button>
    </div>
</template>

<script>
export default {
    props: ["jsonQuestions", "isLoaded"],
    name: "next",
    data() {
        return {
            question: this.jsonQuestions,
            isShown: false,
        };
    },
    watch: {
        // whenever question changes, this function will run
        jsonQuestions: function () {
            this.question = this.jsonQuestions.data.questions;
        },
    },
};
</script>
<style scoped>
.main {
}
button {
    margin: 250px auto;
    display: block;
}
.submit {
    padding: 13px 20px 37px;
}
</style>
