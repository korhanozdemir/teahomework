<template>
    <div class="home">
        <section class="section">
            <div
                v-if="
                    isLoaded &&
                    jsonQuestions.data.questions[
                        $store.state.homework.questionIndex
                    ]
                "
                class="container"
            >
                <h1 class="main-header">
                    {{ jsonQuestions.data.homework_name }}
                </h1>
                <p class="standart-text">
                    Soru
                    <strong>{{
                        $store.state.homework.questionIndex + 1
                    }}</strong>
                </p>
                <p
                    v-if="
                        jsonQuestions.data.questions[
                            $store.state.homework.questionIndex
                        ].question_type === 0
                    "
                    class="standart-text"
                >
                    Doğru şıkkı <strong>SEÇ</strong>!
                </p>
                <p
                    v-if="
                        jsonQuestions.data.questions[
                            $store.state.homework.questionIndex
                        ].question_type === 1
                    "
                    class="subtitle"
                >
                    Boşlukları <strong>DOLDUR</strong>!
                </p>
                <p
                    v-if="
                        jsonQuestions.data.questions[
                            $store.state.homework.questionIndex
                        ].question_type === 3
                    "
                    class="subtitle"
                >
                    Seçenekleri doğru şekilde <strong>EŞLEŞTİR</strong>!
                </p>
                <p
                    v-if="
                        jsonQuestions.data.questions[
                            $store.state.homework.questionIndex
                        ].question_type === 2
                    "
                    class="subtitle"
                >
                    Doğru seçeneği <strong>SÜRÜKLE</strong> ve
                    <strong>BIRAK</strong> !
                </p>
                <p
                    v-if="
                        jsonQuestions.data.questions[
                            $store.state.homework.questionIndex
                        ].question_type === 4
                    "
                    class="subtitle"
                >
                    Doğru kutucukları <strong>İŞARETLE</strong>!
                </p>
                <p
                    v-if="
                        jsonQuestions.data.questions[
                            $store.state.homework.questionIndex
                        ].question_type === 5
                    "
                    class="subtitle"
                >
                    Soruyu çöz ve <strong>YORUMLA</strong>!
                </p>
            </div>
        </section>
        <div v-show="$store.state.homework.homework_started" class="timing">
            <div></div>
            <div style="position: relative; height: 38px;">
                <button class="button is-primary" @click="openCalendar">
                    Takvim
                </button>
                <timer
                    v-show="
                        homework_headers.homework_time > 0 &&
                        !$store.state.homework.isDone
                    "
                    v-on:go-back="$emit('go-back')"
                    v-on:toggle-modal="
                        $emit('toggle-modal', '.time-is-up-modal')
                    "
                    v-on:submitHomework="
                        $emit('submitHomework', '.time-is-up-modal')
                    "
                    :isLoaded="isLoaded"
                    :jsonQuestions.sync="jsonQuestions"
                    :preQuestion.sync="preQuestion"
                />
            </div>
            <div></div>
        </div>
        <div
            class="start-homework"
            v-show="homework !== null"
            v-if="
                !$store.state.homework.homework_started &&
                !$store.state.homework.isDone
            "
        >
            <p>
                <b>{{ homework_headers.title }}</b> adlı ödevi çözmektesiniz. Bu
                ödev <b>{{ homework_headers.question_count }}</b> sorudan
                oluşmaktadır ve
                <span v-if="this.homework_headers.homework_time">
                    zaman kısıtlamalıdır. Süreniz
                    <b>{{ time }}</b> dakikadır.</span
                >
                <span v-else> zaman kısıtlamanız <b>yoktur.</b> </span>
            </p>
            <button class="button is-success" @click="startHomework">
                Başla
            </button>
            <button class="button" @click="$emit('go-back')">Geri Dön</button>
        </div>
        <main-area
            v-show="$store.state.homework.homework_started"
            v-if="$store.state.homework.isHomeworkValid"
            v-on:prev-question="prevQuestion"
            v-on:settler="settler"
            v-on:toggle-show="toggleShow"
            v-on:submit="submit"
            v-on:next-question="nextQuestion"
            v-on:change-question="changeQuestion"
            v-on:screenshot="screenshot"
            :isLoaded="isLoaded"
            :isShown="isShown"
            :jsonQuestions.sync="jsonQuestions"
            :preQuestion.sync="preQuestion"
        />
        <div v-else>
            <div>
                <p
                    style="
                        line-height: 526px;
                        font-size: 18pt;
                        color: red;
                        text-align: center;
                    "
                >
                    Bu ödev problemli, lütfen öğretmeninize danışınız.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import timer from "./timer";
import mainArea from "./main-area.vue";
import Draggable from "gsap/Draggable";
import gsap, { TweenLite } from "gsap";
import "bulma/css/bulma.css";
import StudentService from "../../services/student.service";
import { mapActions, mapGetters } from "vuex";
import html2canvas from "html2canvas";

gsap.registerPlugin(Draggable);

export default {
    name: "Home",
    props: ["homeworkId"],
    data() {
        return {
            isLoaded: false,
            isShown: false,
            jsonQuestions: null,
            student_answers: [],
            preQuestion: String,
            index: 0,
            homework_headers: {
                title: "",
                homework_time: 1,
                question_count: 2,
            },
            homework: null,
        };
    },
    components: {
        mainArea,
        timer,
    },
    methods: {
        nextQuestion() {
            if (
                this.$store.state.homework.questionIndex <
                this.jsonQuestions.data.questions.length - 1
            ) {
                this.$store.commit("homework/nextQuestion");
                this.$store.commit("homework/setTime");

                setTimeout(this.dragger, 100);
                setTimeout(this.matcher, 100);
                if (document.querySelector(".question-area-canvas canvas")) {
                    const elem = document.querySelectorAll(
                        ".question-area-canvas canvas"
                    );
                    for (let i = 0; i < elem.length; i++) {
                        document
                            .querySelector(".question-area-canvas")
                            .removeChild(elem[i]);
                    }
                }
                if (
                    !document
                        .querySelector(".question-area-canvas")
                        .classList.contains("resetted")
                ) {
                    document
                        .querySelector(".question-area-canvas")
                        .classList.add("resetted");
                }
                this.screenshot();
            }
        },
        prevQuestion() {
            if (this.$store.state.homework.questionIndex > 0) {
                this.$store.commit("homework/prevQuestion");
                this.$store.commit("homework/setTime");

                setTimeout(this.dragger, 100);
                setTimeout(this.matcher);
                if (document.querySelector(".question-area-canvas canvas")) {
                    const elem = document.querySelectorAll(
                        ".question-area-canvas canvas"
                    );
                    for (let i = 0; i < elem.length; i++) {
                        document
                            .querySelector(".question-area-canvas")
                            .removeChild(elem[i]);
                    }
                }
                if (
                    !document
                        .querySelector(".question-area-canvas")
                        .classList.contains("resetted")
                ) {
                    document
                        .querySelector(".question-area-canvas")
                        .classList.add("resetted");
                }
                this.screenshot();
            }
        },
        changeQuestion(state) {
            //console.log(await this.screenshot());

            if (state === "prev") {
                this.$store.commit("homework/toggleShow");
                this.index--;
                this.preQuestion = state + this.index;
            }

            if (state === "next") {
                this.$store.commit("homework/toggleShow");
                this.index++;
                this.preQuestion = state + this.index;
            }
        },
        dragger() {
            if (document.getElementsByClassName("answer_image")[0]) {
                var self = this;
                const overlapThreshold = "25%";
                let foundIndex = null; //current droparea index user is hovering
                let overlapped_index = null;
                Draggable.create(document.querySelectorAll(".dragarea"), {
                    type: "x,y",
                    throwProps: true,
                    bounds: ".canvas",
                    onDrag(e) {
                        let i = document.querySelectorAll(".droparea").length;
                        while (--i > -1) {
                            if (
                                this.hitTest(
                                    document.querySelectorAll(".droparea")[i],
                                    overlapThreshold
                                )
                            ) {
                                for (
                                    let j = 0;
                                    j <
                                    document.querySelectorAll(".dragarea")
                                        .length;
                                    j++
                                ) {
                                    if (
                                        this.hitTest(
                                            document.querySelectorAll(
                                                ".dragarea"
                                            )[j],
                                            overlapThreshold
                                        )
                                    ) {
                                        overlapped_index = j;
                                    }
                                }
                                foundIndex = i;
                                TweenLite.set(
                                    document.querySelectorAll(".droparea")[
                                        foundIndex
                                    ],
                                    {
                                        border: "#f99c0b 3px solid",
                                    }
                                );
                                return;
                            } else {
                                foundIndex = null;
                                if (
                                    document.querySelectorAll(
                                        ".empty_rect.droparea"
                                    ).length > 0
                                ) {
                                    TweenLite.set(
                                        document.querySelectorAll(".droparea"),
                                        {
                                            opacity: 1,
                                            border: "2px black solid",
                                        }
                                    );
                                } else {
                                    TweenLite.to(
                                        document.querySelectorAll(".droparea")[
                                            i
                                        ],
                                        0.5,
                                        {
                                            opacity: 1,
                                            border: "none",
                                        }
                                    );
                                }
                            }
                        }
                    },
                    onDragEnd() {
                        if (foundIndex === null) {
                            // move to original pos
                            TweenLite.to(this.target, 0.2, {
                                x: 0,
                                y: 0,
                                width: 281 + "px",
                                height: 47 + "px",
                                backgroundSize: 281 + "px",
                            });
                            TweenLite.to(this.target.children[0], 0.2, {
                                top: "50%",
                            });
                            self.$store.commit(
                                "homework/removeMatching",
                                this.target.id.replace("option", "")
                            );
                            return;
                        } else {
                            //element hit-test passed so i want to postion it in the droparea and scale dragarea to cover droparea
                            const dropBound = document
                                .querySelectorAll(".droparea")
                                [foundIndex].getBoundingClientRect();
                            const dragBound = this.target.getBoundingClientRect();
                            if (
                                document.querySelectorAll(
                                    ".empty_rect.droparea"
                                ).length > 0
                            ) {
                                TweenLite.to(this.target, 0.5, {
                                    opacity: 0,
                                });
                                TweenLite.set(this.target, {
                                    x: 0,
                                    y: 0,
                                });
                                TweenLite.to(this.target, 0.5, {
                                    opacity: 1,
                                });
                                document.querySelectorAll(".droparea")[
                                    foundIndex
                                ].firstChild.firstChild.innerText = this.target.firstChild.firstChild.innerText;

                                TweenLite.set(
                                    document.querySelectorAll(".droparea"),
                                    {
                                        opacity: 1,
                                        border: "2px black solid",
                                    }
                                );
                            } else {
                                TweenLite.to(this.target, 0.2, {
                                    x: "+=" + (dropBound.x - dragBound.x),
                                    y: "+=" + (dropBound.y - dragBound.y + 4),
                                    backgroundSize: dropBound.width + "px",
                                    width: dropBound.width,
                                });
                                if (overlapped_index !== null) {
                                    TweenLite.to(
                                        document.querySelectorAll(".dragarea")[
                                            overlapped_index
                                        ],
                                        0.2,
                                        {
                                            x: 0,
                                            y: 0,
                                            width: 281 + "px",
                                            height: 47 + "px",
                                            backgroundSize: 281 + "px",
                                        }
                                    );
                                    TweenLite.to(
                                        document.querySelectorAll(".dragarea")[
                                            overlapped_index
                                        ].children[0],
                                        0.2,
                                        {
                                            top: "50%",
                                        }
                                    );

                                    overlapped_index = null;
                                }
                                TweenLite.to(this.target.children[0], 0.2, {
                                    top: "38%",
                                });
                                TweenLite.set(
                                    document.querySelectorAll(".droparea"),
                                    {
                                        opacity: 1,
                                        border: "none",
                                    }
                                );
                                let model = {};
                                model.question_index =
                                    self.jsonQuestions.data.questions[
                                        self.$store.state.homework.questionIndex
                                    ].question_index;
                                model.option = this.target
                                    .getAttribute("id")
                                    .replace("option", "");
                                model.option_matched = document
                                    .querySelectorAll(".droparea")
                                    [foundIndex].getAttribute("id")
                                    .replace("empty", "");

                                if (model.option && model.option_matched) {
                                    self.$store.commit(
                                        "homework/addMatching",
                                        model
                                    );
                                }
                            }
                        }
                    },
                });
            }
        },
        matcher() {
            debugger;
            if (document.getElementsByClassName("match")[0]) {
                var clicked = [];
                var hoverElement;
                var lineElem;
                function drawLineXY(fromXY, toXY) {
                    debugger;
                    if (
                        !document.querySelector(".question-area-canvas canvas")
                    ) {
                        lineElem = document.createElement("canvas");
                        lineElem.style.position = "absolute";
                        document
                            .querySelector(".question-area-canvas")
                            .appendChild(lineElem);
                    }
                    lineElem.width = 570;
                    lineElem.height = 530;
                    lineElem.style.left = 0;
                    lineElem.style.top = 0;
                    var ctx = lineElem.getContext("2d");
                    // Use the identity matrix while clearing the canvas

                    ctx.save();
                    ctx.setTransform(1, 0, 0, 1, 0, 0);
                    ctx.clearRect(0, 0, lineElem.width, lineElem.height);
                    ctx.restore();
                    ctx.lineWidth = 4;
                    ctx.strokeStyle = "black";
                    ctx.beginPath();
                    ctx.moveTo(fromXY.x + 10, fromXY.y + 10);
                    ctx.lineTo(toXY.x + 10, toXY.y + 10);
                    ctx.stroke();
                }

                var movaDiv = document.getElementById("mova");
                var movbDiv = document.getElementById("movb");
                function moveHandler(evt) {
                    var movaBounds = movaDiv.getBoundingClientRect();
                    var targets = [];
                    if (clicked.length === 2) {
                        if (
                            document
                                .querySelector(".question-area-canvas")
                                .classList.contains("resetted")
                        ) {
                            clicked = [];
                            targets = [];
                            document
                                .querySelector(".question-area-canvas")
                                .classList.remove("resetted");
                        } else {
                            targets = clicked;
                        }
                    } else if (clicked.length === 1) {
                        targets.push(clicked[0]);
                        if (typeof hoverElement !== "undefined") {
                            targets.push(hoverElement);
                        }
                    }

                    if (targets.length === 2) {
                        var start = eval("(" + targets[0].dataset.coord + ")");
                        var end = eval("(" + targets[1].dataset.coord + ")");
                        drawLineXY(start, end);
                        var movbBounds = movbDiv.getBoundingClientRect();
                        movaDiv.style.left =
                            start.x - movaBounds.width / 2.0 + "px";
                        movaDiv.style.top =
                            start.y - movaBounds.height / 2.0 + "px";
                        movbDiv.style.left =
                            end.x - movbBounds.width / 2.0 + "px";
                        movbDiv.style.top =
                            end.y - movbBounds.height / 2.0 + "px";
                    } else if (targets.length === 1) {
                        var startNearest = eval(
                            "(" + targets[0].dataset.coord + ")"
                        );
                        movaDiv.style.left =
                            startNearest.x - movaBounds.width / 2.0 + "px";
                        movaDiv.style.top =
                            startNearest.y - movaBounds.height / 2.0 + "px";

                        drawLineXY(startNearest, {
                            x:
                                evt.clientX -
                                document
                                    .querySelector(".question-area-canvas")
                                    .getBoundingClientRect().left,
                            y:
                                evt.clientY -
                                document
                                    .querySelector(".question-area-canvas")
                                    .getBoundingClientRect().top,
                        });
                    }
                }
                function clickHandler(evt) {
                    if (clicked.length === 2) {
                        clicked = [];
                    }
                    clicked.push(evt.target);
                }

                function hoverOverHandler(evt) {
                    hoverElement = evt.target;
                }

                function hoverOutHandler(evt) {
                    hoverElement = undefined;
                }

                var attachIds = [];
                for (
                    var i = 0;
                    i < document.querySelectorAll(".match").length;
                    i++
                ) {
                    attachIds.push(
                        document
                            .querySelectorAll(".match")
                            [i].getAttribute("id")
                    );
                }
                for (var ind = 0; ind < attachIds.length; ++ind) {
                    var el = document.getElementById(attachIds[ind]);
                    el.onclick = clickHandler;
                    el.onmouseover = hoverOverHandler;
                    el.onmouseout = hoverOutHandler;
                }
                window.onmousemove = moveHandler;
            }
        },
        async getHomework(homeworkId) {
            await StudentService.GetHomework(homeworkId).then((res) => {
                if (res.data.questions.length > 0) {
                    this.homework = res;
                    if (this.$store.state.homework.isDone) {
                        if (
                            this.$store.state.homework.isDone &&
                            (this.homework.data.is_visible_before_deadline ||
                                (!this.homework.data
                                    .is_visible_before_deadline &&
                                    new Date() >
                                        new Date(
                                            this.$store.state.homework.deadline
                                        )))
                        ) {
                            this.callAnswers();
                            this.setShowAnswers();
                        }
                        const self = this;
                        setTimeout(function () {
                            self.startHomework();
                        }, 200);
                    } else {
                        this.setQuestionCount(res.data.questions.length);
                        this.homework_headers.question_count =
                            res.data.questions.length;
                        this.homework_headers.title = res.data.homework_name;
                        this.homework_headers.homework_time =
                            res.data.homework_time;
                    }
                } else {
                    this.$store.commit("homework/toggleHomeworkValidity");
                }
            });
        },
        toggleShow() {
            this.isShown = !this.isShown;
        },
        settler() {
            for (
                let j = 0;
                j < document.querySelectorAll(".dragarea").length;
                j++
            ) {
                document.querySelectorAll(".dragarea")[j].style.transform =
                    "translate3d(0px, 0px, 0px)";
            }
        },
        submit() {
            if (this.$store.state.homework.isDone) {
                this.$emit("go-back");
            } else {
                this.changeQuestion("prev");
                this.screenshot();
                this.$emit("toggle-modal", ".save-and-exit-modal");
            }
        },
        openCalendar() {
            if (this.$store.state.homework.isDone) {
                this.$emit("go-back");
            } else {
                this.$emit("toggle-modal", ".back-to-calendar-modal");
            }
        },
        startHomework() {
            console.log("started");
            this.isLoaded = true;
            this.jsonQuestions = this.homework;
            this.$store.commit("homework/toggleHomeworkStarted");
            this.$store.commit(
                "homework/setQuestionId",
                this.homework.data.questions[0].id
            );
            this.$store.commit("homework/setTime");

            setTimeout(() => {
                this.dragger();
            }, 100);
            setTimeout(() => {
                this.matcher();
            }, 100);
        },
        async screenshot() {
            if (!this.isDone) {
                this.toggleScreenshot();
                await html2canvas(
                    document.querySelector(".main-question-area"),
                    {
                        allowTaint: true,
                        scale: 0.75,
                    }
                ).then((canvas) => {
                    canvas.toBlob((blob) => {
                        this.toggleScreenshot();
                        const reader = new FileReader();
                        console.log(blob);
                        reader.readAsDataURL(blob);
                        reader.onloadend = () => {
                            this.setScreenshot(reader.result);
                            console.log(reader.result);
                        };
                    });
                });
            }
        },
        ...mapActions({
            callAnswers: "homework/callAnswers",
            setShowAnswers: "homework/setShowAnswers",
            toggleScreenshot: "homework/callToggleScreenshot",
            setScreenshot: "homework/setScreenshot",
            setQuestionCount: "homework/setQuestionCount",
        }),
    },

    mounted() {
        this.getHomework(this.homeworkId);
        this.settler();
    },
    computed: {
        time: function () {
            return (
                (
                    "0" + Math.floor(this.homework_headers.homework_time / 60)
                ).slice(-2) +
                ":" +
                (
                    "0" +
                    (this.homework_headers.homework_time -
                        Math.floor(this.homework_headers.homework_time / 60) *
                            60)
                ).slice(-2)
            );
        },
        ...mapGetters({
            showAnswers: "homework/showAnswers",
            isDone: "homework/isDone",
        }),
    },
};
</script>
<style scoped>
.container {
    text-align: center;
}
.home {
    width: 100%;
}
.timing {
    display: grid;
    grid-template-columns: 1fr 900px 1fr;
}
</style>
<style>
.loading {
    background: url("/images/831.gif") no-repeat;
    height: 60px;
    width: 60px;
}
.loading-outer {
    height: 100%;
    width: 100%;
    z-index: 5;
    background-color: #f2f2f2;
    position: absolute;
    top: 0;
}
* {
    user-select: none;
}

.start-homework {
    text-align: center;
    font-size: 22px;
    border: 2px solid gray;
    padding: 140px 40px;
    margin-left: 156px;
    margin-right: 156px;
}
.start-homework p {
    padding-bottom: 50px;
}
.start-homework button {
    padding: 25px;
    margin: 10px;
}
.main-header {
    color: #363636;
    font-size: 2rem;
    font-weight: 600;
    line-height: 1.125;
}
.standart-text {
    color: #4a4a4a;
    font-size: 1.25rem;
    font-weight: 400;
    line-height: 1.25;
}
.standart-text:nth-child(1) {
    padding-bottom: 10px;
}
.section {
    padding-bottom: 10px;
    padding-top: 10px;
}
</style>
