<template>
    <div class="teacher-main" style="display: grid;">
        <profile-menu></profile-menu>
        <student-reports :key="1" v-if="getTab === 0"></student-reports>
        <student-reports :key="2" v-if="getTab === 1"></student-reports>
    </div>
</template>

<script>
import profileMenu from "../profile-menu";
import { mapActions, mapGetters } from "vuex";
import TeacherService from "../../services/teacher.service";
import StudentReports from "./student-reports";
export default {
    components: { profileMenu, StudentReports },
    props: [],
    name: "teacher",
    data() {
        return {
            homeworks: [],
        };
    },
    methods: {
        ...mapActions({
            setCurrentUser: "setCurrentUser",
        }),
    },
    created() {
        this.setCurrentUser(1);
    },
    mounted() {
        document.querySelector(".teacher-main").style.minHeight = "100vh";
    },
    computed: {
        ...mapGetters({
            getTab: "getTab",
        }),
    },
};
</script>
<style scoped>
.teacher-main {
    display: grid;
    grid-template-columns: 1fr 4fr;
    height: 100%;
}
</style>
