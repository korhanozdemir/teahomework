<template>
    <div class="outer">
        <div v-if="isLoaded" class="answer">
            <div
                class="undefined"
                v-if="!question[$store.state.homework.questionIndex]"
            >
                <div>
                    <p
                        style="
                            line-height: 526px;
                            font-size: 18pt;
                            color: #ff0000;
                        "
                    >
                        Bu soru problemli, lütfen öğretmeninize danışınız.
                    </p>
                </div>
            </div>
            <div v-else>
                <ul
                    v-if="
                        question[$store.state.homework.questionIndex]
                            .question_type === 1 ||
                        question[$store.state.homework.questionIndex]
                            .question_type === 2
                    "
                    class="answers"
                >
                    <li
                        class="container"
                        :key="option.id"
                        v-for="option in question[
                            $store.state.homework.questionIndex
                        ].options"
                    >
                        <div
                            class="answer_image dragarea"
                            :id="'option' + option.id"
                        >
                            <div class="centered">
                                <strong>{{ option.option_text }}</strong>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul
                    v-if="
                        question[$store.state.homework.questionIndex]
                            .question_type === 0
                    "
                    class="answers"
                >
                    <li
                        class="container"
                        :key="'choice' + index"
                        v-for="index in question[
                            $store.state.homework.questionIndex
                        ].option_count"
                    >
                        <div
                            class="choice"
                            :id="'index' + Number(index - 1)"
                            :style="{
                                backgroundImage:
                                    'url(' +
                                    'images/liconlar/option' +
                                    choices[index - 1] +
                                    '.png' +
                                    ')',
                            }"
                            @click="choose(index)"
                        ></div>
                    </li>
                </ul>

                <div
                    class="answers_text"
                    v-if="
                        question[$store.state.homework.questionIndex]
                            .question_type === 5
                    "
                >
                    <textarea
                        placeholder="Cevabını buraya yaz."
                        class="text"
                    ></textarea>
                    <button
                        class="audio-recorder button is-primary"
                        @click="
                            toggleModal('.modal-audio');
                            recorder();
                        "
                    >
                        Ses Kaydet
                    </button>
                </div>
            </div>
            <div
                v-if="this.$store.state.homework.isDone"
                style="
                    height: 100%;
                    position: absolute;
                    z-index: 38;
                    width: 100%;
                    left: 0;
                    top: 0;
                "
            ></div>
        </div>
        <div v-show="!this.$store.state.homework.isShown" class="loading-outer">
            <div class="loading centered"></div>
        </div>
        <div class="modal modal-audio">
            <div
                class="modal-background"
                @click="toggleModal('.modal-audio')"
            ></div>
            <div class="audio-container">
                <div class="record-controls">
                    <button
                        :disabled="this.clip_submit_ok"
                        class="record-button record"
                    >
                        <div class="audio-record-icon record-icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 14c1.66 0 2.99-1.34 2.99-3L15 5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"
                                ></path>
                                <path d="M0 0h24v24H0z" fill="none"></path>
                            </svg>
                        </div>
                    </button>
                    <button
                        :disabled="this.clip_submit_ok"
                        class="record-button stop"
                    >
                        <div class="audio-record-icon stop-icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                            >
                                <path d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M6 6h12v12H6z"></path>
                            </svg>
                        </div>
                    </button>
                </div>

                <div class="clip-container">
                    <div class="clip" v-if="audio_source">
                        <div class="clip-parts clip-part-one">
                            <div
                                v-if="!clip_submit_ok"
                                class="delete"
                                @click="deleteClip()"
                            ></div>
                            <p>Ses Kaydı</p>
                        </div>
                        <div class="clip-parts clip-part-two">
                            <p class="duration">{{ clip_duration }}</p>
                            <button
                                class="record-submit button"
                                :class="{ 'w-90': clip_submit_ok }"
                                @click="submitClip(audio_source)"
                                :disabled="this.clip_submit_ok"
                            >
                                <span v-if="clip_submit_loading"
                                    ><img
                                        src="images/liconlar/loading.svg"
                                        alt=""
                                /></span>
                                <span v-else-if="clip_submit_ok"
                                    >Kaydedildi</span
                                >
                                <span v-else>Kaydet</span>
                            </button>
                        </div>
                    </div>
                    <div class="player-container" v-if="audio_source">
                        <vue-plyr :key="audio_source">
                            <audio>
                                <source :src="audio_source" />
                            </audio>
                        </vue-plyr>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import StudentService from "../../services/student.service";
import TweenLite from "gsap";
import { mapActions, mapGetters } from "vuex";
import getBlobDuration from "get-blob-duration";
export default {
    name: "answerArea",
    props: ["jsonQuestions", "isLoaded", "preQuestion", "isShown"],
    data() {
        return {
            question: this.jsonQuestions,
            choices: ["_a", "_b", "_c", "_d", "_e"],
            answers: {},
            answer_model: {
                option: 3,
                question_index: 1,
                option_matched: true,
                time: 2,
            },
            audio_source: "",
            clip_submit_loading: false,
            clip_submit_ok: false,
            clip_duration: "",
        };
    },
    watch: {
        // whenever question changes, this function will run
        jsonQuestions: function () {
            this.question = this.jsonQuestions.data.questions;
            if ([0, 1, 2, 5].includes(this.question[0].question_type)) {
                this.getData(this.question[0].id);
            }
        },

        preQuestion: function () {
            if (
                !(
                    this.question[this.$store.state.homework.questionIndex]
                        .question_type === 3 ||
                    this.question[this.$store.state.homework.questionIndex]
                        .question_type === 4
                )
            ) {
                if (!this.$store.state.homework.isDone) {
                    this.setData(
                        this.question[this.$store.state.homework.questionIndex]
                            .id
                    );
                }
                if (this.preQuestion.includes("prev")) {
                    this.$emit("prev-question");
                }
                if (this.preQuestion.includes("next")) {
                    this.$emit("next-question");
                }

                if (
                    this.question[this.$store.state.homework.questionIndex]
                        .question_type === 3 ||
                    this.question[this.$store.state.homework.questionIndex]
                        .question_type === 4
                ) {
                    this.$store.commit("homework/callQuestionArea");
                } else {
                    this.getData(
                        this.question[this.$store.state.homework.questionIndex]
                            .id
                    );
                }
            }
        },
        "$store.state.homework.answer_question_area": function () {
            this.getData(
                this.question[this.$store.state.homework.questionIndex].id
            );
        },
    },
    computed: {
        ...mapGetters({
            correctAnswers: "homework/answers",
            showAnswers: "homework/showAnswers",
        }),
    },
    methods: {
        choose(index) {
            for (var i = 0; i < this.choices.length; i++) {
                if (this.choices[i].includes("_correct")) {
                    this.choices[i] = this.choices[i].replace("_correct", "");
                }
                if (i === index - 1) {
                    if (
                        document
                            .querySelectorAll(".choice")
                            [i].classList.contains("chosen")
                    ) {
                        document
                            .querySelectorAll(".choice")
                            [i].classList.remove("chosen");
                    } else {
                        document
                            .querySelectorAll(".choice")
                            [i].classList.add("chosen");
                        this.choices[i] += "_correct";
                    }
                }
            }
            for (
                let j = 0;
                j < document.querySelectorAll(".choice").length;
                j++
            ) {
                document.querySelectorAll(".choice")[j].style.backgroundImage =
                    'url("/images/liconlar/option' + this.choices[j] + '.png")';
                if (!(j === index - 1)) {
                    document
                        .querySelectorAll(".choice")
                        [j].classList.remove("chosen");
                }
            }
        },

        async setData(question_id) {
            let questionResult = [];
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 0
            ) {
                let model = {};
                model.answer = document.querySelector(".chosen")
                    ? document
                          .querySelector(".chosen")
                          .getAttribute("id")
                          .replace("index", "")
                    : "";

                model.time = _.find(this.answers, (answer) => {
                    return Number(answer.question_id) === question_id;
                })
                    ? _.find(this.answers, (answer) => {
                          return Number(answer.question_id) === question_id;
                      }).time +
                      Math.abs(
                          new Date() -
                              this.$store.getters["homework/questionTime"]
                      ) /
                          1000
                    : Math.abs(
                          new Date() -
                              this.$store.getters["homework/questionTime"]
                      ) / 1000;
                model.question_id = question_id;
                questionResult.push(model);
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 1
            ) {
                const time = _.find(this.answers, (answer) => {
                    return Number(answer.question_id) === question_id;
                })
                    ? _.find(this.answers, (answer) => {
                          return Number(answer.question_id) === question_id;
                      }).time +
                      Math.abs(
                          new Date() -
                              this.$store.getters["homework/questionTime"]
                      ) /
                          1000
                    : Math.abs(
                          new Date() -
                              this.$store.getters["homework/questionTime"]
                      ) / 1000;

                for (
                    let i = 0;
                    i < document.querySelectorAll(".empty_rect").length;
                    i++
                ) {
                    let rect = document.querySelectorAll(".empty_rect")[i];
                    let option = _.find(
                        this.question[this.$store.state.homework.questionIndex]
                            .options,
                        (option) => {
                            return (
                                option.option_text ===
                                rect.firstChild.firstChild.innerText
                            );
                        }
                    );
                    if (option) {
                        let model = {};
                        model.question_index = this.question[
                            this.$store.state.homework.questionIndex
                        ].question_index;
                        model.option = rect
                            .getAttribute("id")
                            .replace("empty", "");
                        model.option_matched = option.id;
                        model.time = time;
                        model.question_id = question_id;
                        questionResult.push(model);
                    }
                }

                questionResult = questionResult.length
                    ? questionResult
                    : [{ time: time, question_id: question_id }];
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 2
            ) {
                const time = _.find(this.answers, (answer) => {
                    return Number(answer.question_id) === question_id;
                })
                    ? _.find(this.answers, (answer) => {
                          return Number(answer.question_id) === question_id;
                      }).time +
                      Math.abs(
                          new Date() -
                              this.$store.getters["homework/questionTime"]
                      ) /
                          1000
                    : Math.abs(
                          new Date() -
                              this.$store.getters["homework/questionTime"]
                      ) / 1000;

                this.$store.state.homework.type2Match.forEach((element) => {
                    element.time = time;
                    element.question_id = question_id;
                });

                questionResult = this.$store.state.homework.type2Match.length
                    ? [...this.$store.state.homework.type2Match]
                    : [{ time: time, question_id: question_id }];
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 5
            ) {
                let model = {};
                model.description = document.querySelector(
                    ".answers_text textarea"
                ).value;
                model.time = _.find(this.answers, (answer) => {
                    return Number(answer.question_id) === question_id;
                })
                    ? _.find(this.answers, (answer) => {
                          return Number(answer.question_id) === question_id;
                      }).time +
                      Math.abs(
                          new Date() -
                              this.$store.getters["homework/questionTime"]
                      ) /
                          1000
                    : Math.abs(
                          new Date() -
                              this.$store.getters["homework/questionTime"]
                      ) / 1000;
                model.question_id = question_id;
                questionResult.push(model);
            }

            await StudentService.UpdateAllQuestionResult(
                this.$store.state.homework.homeworkId,
                question_id,
                questionResult
            ).then((res) => {
                console.log(questionResult);
            });
            await this.setStudentAnswer(questionResult);
        },
        async getData(question_id) {
            await StudentService.GetAllQuestionResult(
                this.$store.state.homework.homeworkId,
                question_id
            ).then((res) => {
                this.answers = res.data;
                this.$emit("settler");
                this.cleaner();

                if (this.answers.length > 0) {
                    this.useData();
                }
                if (this.showAnswers) {
                    this.placeAnswer();
                }
                this.$store.commit("homework/toggleShow");
            });
        },
        useData() {
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 0
            ) {
                if (this.answers[0].answer || this.answers[0].answer === "0") {
                    if (!this.showAnswers) {
                        this.choose(Number(this.answers[0].answer) + 1);
                    }
                    document
                        .querySelectorAll(".choice")
                        [Number(this.answers[0].answer)].classList.add(
                            "chosen"
                        );
                }
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 1
            ) {
                if (this.answers[0]) {
                    for (let i = 0; i < this.answers.length; i++) {
                        if (
                            document.querySelector(
                                "#empty" + this.answers[i].option
                            )
                        ) {
                            let option = _.find(
                                this.question[
                                    this.$store.state.homework.questionIndex
                                ].options,
                                (option) => {
                                    return (
                                        option.id ===
                                        Number(this.answers[i].option_matched)
                                    );
                                }
                            );
                            document.querySelector(
                                "#empty" + this.answers[i].option
                            ).firstChild.firstChild.innerText = option
                                ? option.option_text
                                : "";
                        }
                    }
                }
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 2
            ) {
                this.$store.state.homework.type2Match = [...this.answers];
                for (let i = 0; i < this.answers.length; i++) {
                    let answer = document.querySelector(
                        "#option" + this.answers[i].option
                    );
                    if (answer) {
                        const dragBound = answer.getBoundingClientRect();
                        const dropBound = document
                            .querySelector(
                                "#empty" + this.answers[i].option_matched
                            )
                            .getBoundingClientRect();
                        TweenLite.set(answer, {
                            x: "+=" + (dropBound.x - dragBound.x + 4),
                            y: "+=" + (dropBound.y - dragBound.y + 4),
                            backgroundSize: dropBound.width + "px",
                            width: dropBound.width,
                        });
                        TweenLite.set(answer.children[0], {
                            top: "38%",
                        });
                    }
                }
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 5
            ) {
                this.clip_submit_ok = this.answers[0].audio !== null;
                document.querySelector(".answers_text textarea").value = this
                    .answers[0].description
                    ? this.answers[0].description
                    : "";

                this.audio_source = this.answers[0].audio;
                if (this.audio_source) {
                    getBlobDuration(this.audio_source).then((duration) => {
                        const minutes = ("0" + Math.floor(duration / 60)).slice(
                            -2
                        );
                        const seconds = ("0" + Math.round(duration % 60)).slice(
                            -2
                        );
                        this.clip_duration = `${minutes}:${seconds}`;
                    });
                }
            }
        },
        cleaner() {
            document.querySelectorAll(".choice").forEach((element) => {
                element.classList.remove("chosen");
            });
            this.choices = ["_a", "_b", "_c", "_d", "_e"];
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 5
            ) {
                document.querySelector(".answers_text textarea").value = "";
                this.audio_source = "";
                this.clip_duration = "";
                this.clip_submit_ok = false;
            }
        },
        placeAnswer() {
            const correctAnswer = this.correctAnswers.data.find(
                (answer) =>
                    answer.question_id ===
                    this.question[this.$store.state.homework.questionIndex].id
            );
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 0
            ) {
                this.cleaner();
                if (this.answers[0]) {
                    if (
                        String(correctAnswer.correct_answer) !==
                        this.answers[0].answer
                    ) {
                        if (this.answers[0].answer) {
                            this.choices[correctAnswer.correct_answer] +=
                                "_correct";
                            this.choices[Number(this.answers[0].answer)] =
                                this.choices[Number(this.answers[0].answer)] +
                                "_wrong";
                        } else {
                            for (
                                let i = 0;
                                i <
                                this.question[
                                    this.$store.state.homework.questionIndex
                                ].option_count;
                                i++
                            ) {
                                if (correctAnswer.correct_answer === i) {
                                    this.choices[
                                        correctAnswer.correct_answer
                                    ] += "_correct";
                                } else {
                                    this.choices[i] += "_wrong";
                                }
                            }
                        }
                    } else {
                        this.choices[Number(this.answers[0].answer)] +=
                            "_correct";
                    }
                } else {
                    for (
                        let i = 0;
                        i <
                        this.question[this.$store.state.homework.questionIndex]
                            .option_count;
                        i++
                    ) {
                        if (correctAnswer.correct_answer === i) {
                            this.choices[correctAnswer.correct_answer] +=
                                "_correct";
                        } else {
                            this.choices[i] += "_wrong";
                        }
                    }
                }
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 1
            ) {
                if (this.answers[0]) {
                    for (
                        let i = 0;
                        i < correctAnswer.drop_targets.length;
                        i++
                    ) {
                        const student_answer = _.find(
                            this.answers,
                            (answer) => {
                                return (
                                    answer.option ===
                                    String(correctAnswer.drop_targets[i].id)
                                );
                            }
                        );
                        if (student_answer) {
                            if (
                                String(
                                    correctAnswer.drop_targets[i]
                                        .correct_option_id
                                ) === student_answer.option_matched
                            ) {
                                if (
                                    document.querySelector(
                                        "#empty" +
                                            correctAnswer.drop_targets[i].id
                                    )
                                ) {
                                    document.querySelector(
                                        "#empty" +
                                            correctAnswer.drop_targets[i].id
                                    ).firstChild.firstChild.style.color =
                                        "#1eb51e";
                                }
                            } else {
                                if (
                                    document.querySelector(
                                        "#empty" +
                                            correctAnswer.drop_targets[i].id
                                    )
                                ) {
                                    document.querySelector(
                                        "#empty" +
                                            correctAnswer.drop_targets[i].id
                                    ).firstChild.firstChild.style.color =
                                        "#F44336";
                                }
                            }
                        } else {
                        }
                    }
                }

                correctAnswer.drop_targets.forEach((element) => {
                    let option = _.find(
                        this.question[this.$store.state.homework.questionIndex]
                            .options,
                        (option) => {
                            return option.id === element.correct_option_id;
                        }
                    );
                    document.getElementById("answer" + element.id).title =
                        option.option_text;
                });
                $('[data-toggle="tooltip"]').tooltip({
                    trigger: "click",
                });
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 2
            ) {
                for (let i = 0; i < correctAnswer.drop_targets.length; i++) {
                    const student_answer = _.find(this.answers, (answer) => {
                        return (
                            answer.option_matched ===
                            String(correctAnswer.drop_targets[i].id)
                        );
                    });
                    if (student_answer) {
                        if (
                            String(
                                correctAnswer.drop_targets[i].correct_option_id
                            ) === student_answer.option
                        ) {
                            if (
                                document.querySelector(
                                    "#option" +
                                        correctAnswer.drop_targets[i]
                                            .correct_option_id
                                )
                            ) {
                                document
                                    .querySelector(
                                        "#option" +
                                            correctAnswer.drop_targets[i]
                                                .correct_option_id
                                    )
                                    .classList.add("answer_image_correct");
                            }
                        } else {
                            if (
                                document.querySelector(
                                    "#option" + student_answer.option
                                )
                            ) {
                                document
                                    .querySelector(
                                        "#option" + student_answer.option
                                    )
                                    .classList.add("answer_image_wrong");
                            }
                        }
                    }
                }

                correctAnswer.drop_targets.forEach((element) => {
                    let option = _.find(
                        this.question[this.$store.state.homework.questionIndex]
                            .options,
                        (option) => {
                            return option.id === element.correct_option_id;
                        }
                    );
                    document.getElementById("answer" + element.id).title =
                        option.option_text;
                });
                $('[data-toggle="tooltip"]').tooltip({
                    trigger: "click",
                });
            }
        },
        onResult(data) {
            console.log("The blob data:", data);
            const reader = new FileReader();
            reader.readAsDataURL(data);
            reader.onloadend = () => {
                console.log(reader.result);
                this.audio_blob = reader.result
                    ? reader.result.split(",")[1]
                    : "";
                this.player_key += 1;
            };
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
        recorder() {
            if (!this.clip_submit_ok) {
                const record = document.querySelector(".record");
                const stop = document.querySelector(".stop");
                if (
                    navigator.mediaDevices &&
                    navigator.mediaDevices.getUserMedia
                ) {
                    console.log("getUserMedia supported.");
                    navigator.mediaDevices
                        .getUserMedia(
                            // constraints - only audio needed for this app
                            {
                                audio: true,
                            }
                        )
                        // Success callback
                        .then((stream) => {
                            const mediaRecorder = new MediaRecorder(stream);
                            record.onclick = function () {
                                mediaRecorder.start();
                                if (
                                    !document
                                        .querySelector(".record-icon")
                                        .classList.contains(
                                            "record-button-pulse"
                                        )
                                ) {
                                    document
                                        .querySelector(".record-icon")
                                        .classList.toggle(
                                            "record-button-pulse"
                                        );
                                }
                                console.log(mediaRecorder.state);
                                console.log("recorder started");
                            };
                            let chunks = [];

                            mediaRecorder.ondataavailable = function (e) {
                                chunks.push(e.data);
                            };
                            stop.onclick = function () {
                                mediaRecorder.stop();
                                document
                                    .querySelector(".record-icon")
                                    .classList.remove("record-button-pulse");
                                console.log(mediaRecorder.state);
                                console.log("recorder stopped");
                            };
                            mediaRecorder.onstop = () => {
                                const blob = new Blob(chunks, {
                                    type: "audio/ogg; codecs=opus",
                                });
                                getBlobDuration(blob).then((duration) => {
                                    const minutes = (
                                        "0" + Math.floor(duration / 60)
                                    ).slice(-2);
                                    const seconds = (
                                        "0" + Math.round(duration % 60)
                                    ).slice(-2);
                                    this.clip_duration = `${minutes}:${seconds}`;
                                });

                                const callback = (reader) => {
                                    this.audio_source = reader.target.result;
                                };
                                this.blob2Base64(blob, callback);
                            };
                        })
                        // Error callback
                        .catch(function (err) {
                            console.log(
                                "The following getUserMedia error occured: " +
                                    err
                            );
                        });
                } else {
                    console.log("getUserMedia not supported on your browser!");
                }
            }
        },
        deleteClip() {
            const confirmation = confirm(
                "Bu kaydı silmek istediğinize emin misiniz?"
            );
            if (confirmation) {
                this.audio_source = "";
            }
        },
        submitClip(audio) {
            const confirmation = confirm(
                "Bu kaydı kaydetmek istediğinize emin misiniz? Onayladıktan sonra bir daha değişiklik yapamayacaksınız."
            );
            if (confirmation) {
                this.clip_submit_loading = true;
                StudentService.setAudio(
                    this.$store.state.homework.homeworkId,
                    this.$store.state.homework.questionId,
                    {
                        audio: audio,
                    }
                )
                    .then(() => {
                        this.clip_submit_loading = false;
                        document.querySelector(".record-submit").style.width =
                            "90px";
                        this.clip_submit_ok = true;
                    })
                    .catch((error) => {
                        console.log(error);
                        this.clip_submit_loading = false;
                        this.clip_submit_ok = false;
                    });
            }
        },
        blob2Base64(blob, callback) {
            const reader = new window.FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = callback;
        },
        ...mapActions({
            setStudentAnswer: "homework/setStudentAnswer",
        }),
    },
};
</script>
<style>
textarea {
    -webkit-user-select: text; /* Chrome, Opera, Safari */
    -moz-user-select: text; /* Firefox 2+ */
    -ms-user-select: text; /* IE 10+ */
    user-select: text; /* Standard syntax */
}
.plyr--full-ui input[type="range"] {
    color: #747474;
}
.plyr__controls__item:first-child:hover {
    background-color: #747474;
}
</style>
<style scoped>
@-webkit-keyframes pulse {
    0% {
        -webkit-box-shadow: 0 0 0 0 #05cbcd;
    }
    70% {
        -webkit-box-shadow: 0 0 0 10px #05cbcd;
    }
    100% {
        -webkit-box-shadow: 0 0 0 0 #05cbcd;
    }
}
@keyframes pulse {
    0% {
        -moz-box-shadow: 0 0 0 0 #05cbcd;
        box-shadow: 0 0 0 0 #05cbcd;
    }
    70% {
        -moz-box-shadow: 0 0 0 10px #05cbcd;
        box-shadow: 0 0 0 10px #05cbcd;
    }
    100% {
        -moz-box-shadow: 0 0 0 0 #05cbcd;
        box-shadow: 0 0 0 0 #05cbcd;
    }
}
.outer {
    position: relative;
    border: 2px solid gray;
    background-color: #f2f2f2;
}
.choice {
    height: 60px;
    width: 60px;
    margin: auto;
    cursor: pointer;
    background-repeat: no-repeat;
    transition: background-image 200ms linear;
}
.container {
    padding-left: 9px;
    margin-top: 10px;
    z-index: 5;
    cursor: grab;
}
ul {
    padding-top: 20px;
    padding-left: 0;
}
.answer_image {
    background: url("../../../assets/liconlar/matching_option_image.png")
        no-repeat;
    height: 47px;
    width: 281px;
}
.answer_image_wrong {
    background: url("../../../assets/liconlar/matching_option_image_wrong.png")
        no-repeat !important;
    height: 47px;
    width: 281px;
    background-size: 200px !important;
}
.answer_image_correct {
    background: url("../../../assets/liconlar/matching_option_image_correct.png")
        no-repeat !important;
    height: 47px;
    width: 281px;
    background-size: 200px !important;
}
.centered {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.answers_text {
    padding: 20px;
}
.text {
    height: 466px;
    width: 265px;
    border-radius: 10px;
    resize: none;
    padding: 20px;
}
.audio-container {
    width: 40vw;
    background-color: #fafafa;
    height: 400px;
    position: relative;
    border-radius: 10px;
    flex-direction: column;
    align-items: center;
    padding: 16px;
}
.record-controls {
    position: relative;
    display: flex;
    justify-content: center;
    padding: 20px;
    gap: 10px;
}
.audio-recorder {
    width: 100%;
    border-radius: 28px;
    margin-top: 10px;
    font-size: 19px;
    z-index: 39;
}
.record-button {
    border: none;
    background-color: #fafafa;
    outline: none;
}
.audio-record-icon {
    fill: #747474;
    border-radius: 50%;
    border: 1px solid #05cbcd;
    background-color: #ffffff;
    padding: 5px;
    cursor: pointer;
    transition: 0.2s;
}
.record-icon {
    width: 65px;
    height: 65px;
    line-height: 65px;
    box-shadow: 0 2px 5px 1px rgba(158, 158, 158, 0.5);
}
.stop-icon {
    width: 40px;
    height: 40px;
    line-height: 40px;
}
.audio-record-icon svg {
    vertical-align: inherit;
}
.clip-container {
    display: flex;
    flex-direction: column;
    height: 250px;
    justify-content: space-between;
}
.clip {
    display: flex;
    justify-content: space-between;
    font-weight: 600;
    background-color: white;
    border-radius: 10px;
    border: 1px solid #e7e6e6;
    padding: 10px;
}

.clip-parts {
    display: flex;
    align-items: center;
    font-size: 1rem;
    gap: 15px;
}
.clip-part-two {
    justify-self: flex-end;
}
.clip-part-one {
    justify-self: flex-start;
}
.player-container {
    justify-self: flex-end;
}
.record-submit {
    background-color: #05cbcd;
    border-color: transparent;
    color: #fff;
    width: 70px;
    height: 30px;
    font-size: 15px;
    font-weight: 500;
    letter-spacing: 0.7px;
}
.record-submit:hover,
.record-submit:focus {
    color: white;
}
.record-submit[disabled] {
    background-color: #05cbcd;
}
.delete {
    min-width: 15px;
    min-height: 15px;
    background-color: #ff7900;
}
.delete:hover,
.delete:focus {
    background-color: #ff9434;
}
.player-container {
    background-color: white;
    border-radius: 10px;
    border: 1px solid #e7e6e6;
    padding: 2px;
}
.w-90 {
    width: 90px;
}
.record-button-pulse {
    animation: pulse 0.75s linear infinite;
}
strong {
    color: #5679b3;
}
</style>
