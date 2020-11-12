<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import StudentService from "../../services/student.service";
import Home from "./Home";
import trLocale from "@fullcalendar/core/locales/tr";
import { mapGetters } from "vuex";

export default {
    name: "test-calendar",

    components: {
        FullCalendar,
        Home, // make the <FullCalendar> tag available
    },

    data: function () {
        return {
            calendarOptions: {
                nextDayThreshold: "01:00:00",
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin, // needed for dateClick
                ],
                locale: trLocale,
                eventTimeFormat: {
                    // like '14:30:00'
                    hour: "2-digit",
                    minute: "2-digit",
                    meridiem: false,
                    hour12: false,
                },
                headerToolbar: {
                    left: "prev,next",
                    center: "title",
                    right: "today",
                },
                initialView: "dayGridMonth",
                events: [],
                editable: false,
                selectable: false,
                selectMirror: true,
                dayMaxEvents: false,
                weekends: true,
                select: false,
                eventClick: this.callHomeworkModule,
                eventsSet: false,
                eventDisplay: "block",
                /* you can update a remote database when these fire:
                    eventAdd:
                    eventChange:
                    eventRemove:
                    */
            },
            isHomeworkOpened: false,
            homeworkId: null,
            eventGuid: 0,
            completedHomeworks: [],
        };
    },

    methods: {
        callHomeworkModule(clickInfo) {
            const event = clickInfo.event._def;
            this.homeworkId = event.publicId;
            this.$store.commit(
                "homework/setDeadline",
                clickInfo.event._instance.range.end
            );
            this.$store.commit("homework/setHomeworkId", event.publicId);
            if (this.completedHomeworks.includes(Number(event.publicId))) {
                this.$store.commit("homework/isDone");
            }
            this.isHomeworkOpened = true;
        },
        submitHomework(className) {
            this.$store.commit("homework/finishHomework");

            this.toggleModal(className);
        },
        async createEvents() {
            let INITIAL_EVENTS = [];
            await StudentService.GetHomework().then((res) => {
                const previous = res.data.previous;
                const future = res.data.future;

                if (previous.previousLateDone.length > 0) {
                    for (let i = 0; i < previous.previousLateDone.length; i++) {
                        if (!previous.previousLateDone[i].homework) {
                            continue;
                        }
                        let model = {};
                        model.id = previous.previousLateDone[i].homework_id;
                        model.title =
                            previous.previousLateDone[i].homework.homework_name;
                        model.start = previous.previousLateDone[i].deadline
                            .split(" ")
                            .join("T");
                        model.end =
                            previous.previousLateDone[i].deadline.split(
                                " "
                            )[0] +
                            "T" +
                            this.minuteAdder(
                                previous.previousLateDone[i].deadline
                            );
                        model.backgroundColor = "rgba(243,158,98,0.1)";
                        model.borderColor = "#F39E62";
                        model.textColor = "#F39E62";
                        INITIAL_EVENTS.push(model);
                        this.completedHomeworks.push(model.id);
                    }
                }
                if (previous.previousSuccess.length > 0) {
                    for (let i = 0; i < previous.previousSuccess.length; i++) {
                        if (!previous.previousSuccess[i].homework) {
                            continue;
                        }
                        let model = {};
                        model.id = previous.previousSuccess[i].homework_id;
                        model.title =
                            previous.previousSuccess[i].homework.homework_name;
                        model.start = previous.previousSuccess[i].deadline
                            .split(" ")
                            .join("T");
                        model.end =
                            previous.previousSuccess[i].deadline.split(" ")[0] +
                            "T" +
                            this.minuteAdder(
                                previous.previousSuccess[i].deadline
                            );
                        model.backgroundColor = "rgba(92, 201, 125, 0.098)";
                        model.borderColor = "#5cc97d";
                        model.textColor = "#5cc97d";
                        INITIAL_EVENTS.push(model);
                        this.completedHomeworks.push(model.id);
                    }
                }
                if (previous.previousWaiting.length > 0) {
                    for (let i = 0; i < previous.previousWaiting.length; i++) {
                        if (!previous.previousWaiting[i].homework) {
                            continue;
                        }
                        let model = {};
                        model.id = previous.previousWaiting[i].homework_id;
                        model.title =
                            previous.previousWaiting[i].homework.homework_name;
                        model.start = previous.previousWaiting[i].deadline
                            .split(" ")
                            .join("T");
                        model.end =
                            previous.previousWaiting[i].deadline.split(" ")[0] +
                            "T" +
                            this.minuteAdder(
                                previous.previousWaiting[i].deadline
                            );
                        model.backgroundColor = "rgba(239, 83, 84, 0.098)";
                        model.borderColor = "#ef5354";
                        model.textColor = "#ef5354";
                        INITIAL_EVENTS.push(model);
                    }
                }

                if (future.futureDone.length > 0) {
                    for (let i = 0; i < future.futureDone.length; i++) {
                        if (!future.futureDone[i].homework) {
                            continue;
                        }
                        let model = {};
                        model.id = future.futureDone[i].homework_id;
                        model.title =
                            future.futureDone[i].homework.homework_name;
                        model.start = future.futureDone[i].deadline
                            .split(" ")
                            .join("T");
                        model.end =
                            future.futureDone[i].deadline.split(" ")[0] +
                            "T" +
                            this.minuteAdder(future.futureDone[i].deadline);
                        model.backgroundColor = "rgba(92,201,125,0.09)";
                        model.borderColor = "#5cc97d";
                        model.textColor = "#5cc97d";
                        INITIAL_EVENTS.push(model);
                        this.completedHomeworks.push(model.id);
                    }
                }
                if (future.futureWaiting.length > 0) {
                    for (let i = 0; i < future.futureWaiting.length; i++) {
                        if (!future.futureWaiting[i].homework) {
                            continue;
                        }
                        let model = {};

                        model.id = future.futureWaiting[i].homework_id;
                        model.title =
                            future.futureWaiting[i].homework.homework_name;
                        model.start = future.futureWaiting[i].deadline
                            .split(" ")
                            .join("T");
                        model.end =
                            future.futureWaiting[i].deadline.split(" ")[0] +
                            "T" +
                            this.minuteAdder(future.futureWaiting[i].deadline);
                        model.backgroundColor = "#376eea19";
                        model.borderColor = "#376eea";
                        model.textColor = "#376eea";
                        INITIAL_EVENTS.push(model);
                    }
                }
            });
            this.calendarOptions.events = [...INITIAL_EVENTS];
        },
        createEventId() {
            return String(this.eventGuid++);
        },
        minuteAdder(dateObject) {
            let date = new Date(dateObject);
            date.setSeconds(date.getSeconds() - 1);
            return date.getHours() + ":" + ("0" + date.getMinutes()).slice(-2);
        },
        goBack() {
            location.reload();
        },
        toggleModal(className) {
            if (
                document
                    .querySelector(className)
                    .classList.contains("is-active")
            ) {
                document.querySelector(className).classList.remove("is-active");
            } else {
                document.querySelector(className).classList.add("is-active");
            }
        },
    },
    mounted() {
        this.createEvents();
    },
    computed: {
        ...mapGetters({
            isScreenshot: "homework/isScreenshot",
            questionCount: "homework/getQuestionCount",
        }),
        disabled: function () {
            return !!this.isScreenshot;
        },
    },
};
</script>

<template>
    <div class="calendar">
        <div class="save-and-exit-modal modal">
            <div class="modal-background" @click="toggleModal('')"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Uyarı</p>
                    <button
                        class="delete"
                        @click="toggleModal('.save-and-exit-modal')"
                        aria-label="close"
                    ></button>
                </header>
                <section class="modal-card-body">
                    Değişiklikleri kaydedip çıkmak istediğinize emin misiniz?
                </section>
                <footer class="modal-card-foot">
                    <button
                        :disabled="disabled"
                        class="button is-success"
                        @click="
                            submitHomework('.save-and-exit-modal');
                            goBack();
                        "
                    >
                        KAYDET VE ÇIK
                    </button>
                    <button
                        class="button"
                        @click="toggleModal('.save-and-exit-modal')"
                    >
                        İPTAL
                    </button>
                </footer>
            </div>
        </div>
        <div class="back-to-calendar-modal modal">
            <div
                class="modal-background"
                @click="toggleModal('.back-to-calendar-modal')"
            ></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Uyarı</p>
                    <button
                        class="delete"
                        @click="toggleModal('.back-to-calendar-modal')"
                        aria-label="close"
                    ></button>
                </header>
                <section class="modal-card-body">
                    Ödeviniz hala devam ediyor. Takvime geri dönmek istediğinize
                    emin misiniz? Kalan süreniz dahilinde geri dönüp çözmeye
                    devam edebilirsiniz.
                </section>
                <footer class="modal-card-foot">
                    <button
                        class="button is-success"
                        @click="
                            toggleModal('.back-to-calendar-modal');
                            goBack();
                        "
                    >
                        KAYDET VE ÇIK
                    </button>
                    <button
                        class="button"
                        @click="toggleModal('.back-to-calendar-modal')"
                    >
                        İPTAL
                    </button>
                </footer>
            </div>
        </div>
        <div class="time-is-up-modal modal">
            <div
                class="modal-background"
                @click="toggleModal('.time-is-up-modal')"
            ></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Uyarı</p>
                    <button
                        class="delete"
                        @click="toggleModal('.time-is-up-modal')"
                        aria-label="close"
                    ></button>
                </header>
                <section class="modal-card-body">
                    Süreniz dolduğu için ödevinizin yaptığınız kadarı sisteme
                    yüklendi. Bundan sonra yapacaklarınzıın puanınıza bir etkisi
                    olmayacaktır.
                </section>
            </div>
        </div>
        <Home
            v-on:go-back="goBack"
            v-on:submitHomework="submitHomework"
            v-on:toggle-modal="toggleModal"
            v-if="isHomeworkOpened"
            :homeworkId="homeworkId"
        ></Home>

        <div v-else class="demo-app-main">
            <FullCalendar class="demo-app-calendar" :options="calendarOptions">
                <template v-slot:eventContent="arg">
                    <b>{{ arg.timeText }}</b>
                    <i>{{ arg.event.title }}</i>
                </template>
            </FullCalendar>
        </div>
    </div>
</template>

<style scoped lang="css">
h2 {
    margin: 0;
    font-size: 16px;
}
.calendar {
    display: flex;
    flex-grow: 3;
}

ul {
    margin: 0;
    padding: 0 0 0 1.5em;
}

li {
    margin: 1.5em 0;
    padding: 0;
}
</style>
