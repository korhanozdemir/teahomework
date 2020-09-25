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
    </div>
</template>

<script>
import StudentService from "../../services/student.service";
import TweenLite from "gsap";
import { mapActions, mapGetters } from "vuex";

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
        };
    },
    watch: {
        // whenever question changes, this function will run
        jsonQuestions: function () {
            this.question = this.jsonQuestions.data.questions;
            this.getData(this.question[0].id);
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
                        questionResult.push(model);
                    }
                }
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
                });

                questionResult = [...this.$store.state.homework.type2Match];
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
                if (model.description) {
                    questionResult.push(model);
                }
            }

            await StudentService.UpdateAllQuestionResult(
                this.$store.state.homework.homeworkId,
                question_id,
                questionResult
            ).then((res) => {
                console.log(questionResult);
            });
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
                document.querySelector(".answers_text textarea").value = this
                    .answers[0].description
                    ? this.answers[0].description
                    : "";
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
    },
};
</script>

<style scoped>
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
    height: 530px;
    width: 265px;
    border-radius: 10px;
    resize: none;
    padding: 20px;
}
strong {
    color: #5679b3;
}
</style>
