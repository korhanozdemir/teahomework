<template>
    <div class="main">
        <button v-if="this.questionIndex > 0" class="button is-primary" @click="$emit('prev-question')">PREV</button>
    </div>
</template>

<script>
    export default {
        name: "previous",
        props: ["jsonQuestions","questionIndex"]

    }
</script>

<style scoped>
    .main{
        background-color: white;
        border-right: 1px solid gray ;
    }
    button{
        margin: 250px auto;
        display: block;

    }
</style>
