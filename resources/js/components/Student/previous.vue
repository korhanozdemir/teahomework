<template>
    <div class="main">
        <button
            :disabled="!this.$store.state.homework.isShown"
            v-if="this.$store.state.homework.questionIndex > 0"
            class="button is-primary"
            @click="$emit('prev-question')"
        >
            ÖNCEKİ
        </button>
    </div>
</template>

<script>
export default {
    name: "previous",
    props: ["jsonQuestions"],
};
</script>

<style scoped>
.main {
}
button {
    margin: 250px auto;
    display: block;
}
</style>
