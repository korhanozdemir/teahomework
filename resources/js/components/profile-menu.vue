<template>
    <div v-if="!$store.state.homework.homework_started" class="main">
        <div class="profile profile-student" v-if="getUser === 0">
            <div class="profile-image">
                <img :src="file_name" />
            </div>
            <div class="profile-info">
                <p class="name">{{ name }}</p>
                <p class="class is-inline pr-2">{{ class_number }}</p>
                <p class="student-number is-inline pl-2">
                    {{ student_number }}
                </p>
            </div>
            <div class="profile-menu">
                <button
                    @click="setProfileTab(0)"
                    style="color: #258c14;"
                    :class="{ active: getTab === 0 }"
                >
                    <img src="images/liconlar/cal_icon.png" alt="" />
                    <p>TAKVİM</p>
                </button>
                <button
                    @click="setProfileTab(1)"
                    style="color: #004ed9;"
                    :class="{ active: getTab === 1 }"
                >
                    <img src="images/liconlar/list_icon.png" alt="" />
                    <p>ÖDEV LİSTESİ</p>
                </button>
                <button
                    @click="setProfileTab(2)"
                    style="color: #ff9200;"
                    :class="{ active: getTab === 2 }"
                >
                    <img src="images/liconlar/notification_icon.png" alt="" />
                    <p>BİLDİRİM</p>
                    <div class="read" v-if="getNotificationStatus"></div>
                </button>
                <button @click="logOut">
                    <img src="images/liconlar/logout_icon.png" alt="" />
                    <p>ÇIKIŞ</p>
                </button>
            </div>
        </div>
        <div
            class="profile profile-teacher"
            v-if="getUser === 1 || getUser === 2"
        >
            <div class="profile-image">
                <img :src="file_name" />
            </div>
            <div class="profile-info">
                <p class="name">{{ name }}</p>
            </div>
            <div class="profile-menu">
                <button
                    @click="setProfileTab(0)"
                    style="color: #e96562;"
                    :class="{ active: getTab === 0 }"
                >
                    <img src="images/liconlar/homework_icon.png" alt="" />
                    <p>ÖDEVLER</p>
                </button>
                <button
                    @click="setProfileTab(1)"
                    style="color: #5e9c76;"
                    :class="{ active: getTab === 1 }"
                >
                    <img src="images/liconlar/class_icon.png" alt="" />
                    <p>SINIFLAR</p>
                </button>
                <button
                    @click="setProfileTab(2)"
                    style="color: #ff9200;"
                    :class="{ active: getTab === 2 }"
                >
                    <img src="images/liconlar/notification_icon.png" alt="" />

                    <p>BİLDİRİM</p>
                </button>
                <button @click="logOut">
                    <img src="images/liconlar/logout_icon.png" alt="" />
                    <p>ÇIKIŞ</p>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import StudentService from "../services/student.service";

export default {
    props: [],
    name: "ProfileMenu",
    data() {
        return {
            file_name: "",
            name: "",
            class_number: "",
            student_number: "",
            notification: 0,
        };
    },
    methods: {
        logOut() {
            window.location.href = "/logout";
        },
        ...mapActions({
            setProfileTab: "setProfileTab",
            setNotificationStatus: "setNotificationStatus",
        }),
        async checkNotifications() {
            if (this.getUser === 0) {
                await StudentService.getNotifications().then((res) => {
                    if (res) {
                        this.setNotificationStatus(
                            res.data.notifications.find((element) => {
                                return element.pivot.is_read === 0;
                            })
                                ? 1
                                : 0
                        );
                    }
                });
            }
        },
        async setProfile() {
            await StudentService.GetProfileInfo().then((res) => {
                if (res) {
                    this.name = res.data[0].name;
                    this.student_number = res.data[0].code;
                    this.file_name = res.data[0].photo;
                    this.class_number = res.data[0].clsses.find((element) => {
                        return element.is_class_mixed === 0;
                    })
                        ? res.data[0].clsses.find((element) => {
                              return element.is_class_mixed === 0;
                          }).class_name
                        : "";
                }
            });
        },
    },
    computed: {
        ...mapGetters({
            getTab: "getTab",
            getUser: "getUser",
            getNotificationStatus: "getNotificationStatus",
        }),
    },
    mounted() {
        this.setProfile();
        this.checkNotifications();
    },
    watch: {
        getTab: function () {
            if (!this.notification) {
                this.checkNotifications();
            }
        },
    },
};
</script>
<style scoped>
.main {
    text-align: center;
    box-shadow: 0 3px 27px 8px #e3e4e4;
    color: #b5b5b5;
    font-family: Helvetica;
    width: 200px;
}
.profile-image img {
    height: 90px;
    width: 90px;
    border-radius: 45px;
    object-fit: cover;
}
.profile {
    padding: 35px 30px 0 30px;
}
.profile-info {
    padding-top: 10px;
    font-size: 14px;
}
.profile-menu {
    margin-top: 20px;
}
.profile-menu p {
    font-size: 12px;
    font-weight: 600;
    vertical-align: middle;
    display: inline-block;
    color: #b5b5b5;
    font-family: Helvetica;
    padding-left: 15px;
    margin: auto 0;
}
.profile-menu img {
    height: 14px;
    padding-left: 10px;
    margin: auto 0;
}
.profile-menu button {
    border: none;
    height: 30px;
    background-color: inherit;
    width: 100%;
    text-align: left;
    display: flex;
}
button.active {
    box-shadow: -5px 0 0 0;
}
.active p {
    color: black;
}
.read {
    width: 7px;
    height: 7px;
    background-color: #ff9200;
    margin: auto;
    border-radius: 4px;
}
.profile {
    position: sticky;
    top: 0;
}
</style>
