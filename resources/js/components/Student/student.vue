<template>
    <div
        class="student-main"
        :style="
            $store.state.homework.homework_started
                ? { display: 'block' }
                : { display: 'grid' }
        "
    >
        <profile-menu
            v-if="!$store.state.homework.homework_started"
        ></profile-menu>
        <test-calendar v-if="getTab === 0"></test-calendar>
        <list v-if="getTab === 1"></list>
        <notifications v-if="getTab === 2"></notifications>
    </div>
</template>

<script>
import testCalender from "./test-calendar";
import profileMenu from "../profile-menu";
import list from "./list";
import notifications from "./notifications";

import { mapActions, mapGetters } from "vuex";
import StudentService from "../../services/student.service";

export default {
    components: { testCalender, profileMenu, list, notifications },
    props: [],
    name: "student",
    data() {
        return {};
    },
    methods: {
        ...mapActions({
            setCurrentUser: "setCurrentUser",
        }),
    },
    mounted() {
        document.querySelector(".student-main").style.minHeight = "100vh";
        this.setCurrentUser(0);
    },
    computed: {
        ...mapGetters({
            getTab: "getTab",
        }),
    },
};
</script>
<style scoped>
.student-main {
    display: grid;
    grid-template-columns: 1fr 4fr;
    height: 100%;
}
</style>
