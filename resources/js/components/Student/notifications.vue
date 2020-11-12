<template>
    <div class="main-notifications">
        <div v-if="notifications.length">
            <button
                class="notification"
                :key="notification.id"
                v-for="notification in notifications"
                @click="
                    callAction(notification.ntype, notification.homework_id)
                "
            >
                <div
                    :style="
                        notification.ntype === 1
                            ? {
                                  backgroundImage:
                                      'url(' +
                                      'images/liconlar/homework_icon_pencil.png' +
                                      ')',
                              }
                            : {
                                  backgroundImage:
                                      'url(' +
                                      'images/liconlar/comment_icon.png' +
                                      ')',
                              }
                    "
                    class="image"
                ></div>
                <div class="text-block">
                    <p class="date">
                        {{ formatDate(notification.created_at) }}
                    </p>
                    <p class="content-text" v-if="notification.ntype === 1">
                        <span>
                            {{ notification.course_name }}
                        </span>
                        dersi için
                        <span>
                            {{ notification.homework_name }}
                        </span>
                        adında yeni bir ödeviniz var.
                    </p>
                    <p class="content-text" v-if="notification.ntype === 2">
                        <span>
                            {{ notification.homework_name }}
                        </span>
                        adlı ödeve öğretmeniniz tarafından yeni bir yorum
                        eklendi.
                    </p>
                </div>
                <div class="read" v-if="!notification.is_read"></div>
            </button>
        </div>
        <div v-else>
            <div class="notification">
                <div class="text-block">
                    <p class="content-text" style="text-align: center;">
                        Henüz bir bildirimin yok.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import StudentService from "../../services/student.service";
import moment from "moment";
import { mapActions, mapGetters } from "vuex";
export default {
    name: "notifications",
    data() {
        return {
            notifications: [],
        };
    },
    mounted() {
        StudentService.getNotifications().then((res) => {
            if (res) {
                this.notifications = res.data.notifications;
                this.setNotificationStatus(
                    this.notifications.find((element) => {
                        return element.is_read === 0;
                    })
                        ? 1
                        : 0
                );
            }
        });
    },

    methods: {
        formatDate(date) {
            moment.locale("tr");
            return moment(date).format("LLLL");
        },
        ...mapActions({
            setNotificationStatus: "setNotificationStatus",
            setTab: "setProfileTab",
            setNotificationClicked: "setNotificationClicked",
            setNotificationHomeworkID: "setNotificationHomeworkID",
        }),
        callAction(notification_id, homework_id) {
            if (notification_id === 1) {
                this.setNotificationClicked();
                this.setNotificationHomeworkID(homework_id);
                this.setTab(1);
            }
            if (notification_id === 2) {
                this.setTab(1);
            }
        },
    },
    destroyed() {
        StudentService.setNotifications();
    },
};
</script>

<style scoped>
.main-notifications {
    width: 50%;
    margin: 65px auto;
}
.image {
    min-width: 48px;
    height: 48px;
    flex-grow: 0.3;
    background-size: 48px;
    background-repeat: no-repeat;
    margin: auto;
}
.notification {
    background-color: white;
    border-radius: 0;
    border: 1px solid gainsboro;
    display: flex;
    min-width: 220px;
    width: 100%;
    padding: 0.75rem 2.5rem 0.75rem 1.5rem;
}
.text-block {
    flex-grow: 4;
    padding: 0 10px;
}
.date {
    color: lightgray;
    text-align: left;
    font-size: 11px;
}
.content-text {
    text-align: left;
}
.content-text span {
    font-weight: 600;
}
.read {
    background-color: #2fb6ab;
    min-width: 12px;
    min-height: 12px;
    margin: auto 0;
    border-radius: 6px;
}
</style>
