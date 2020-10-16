<template>
    <div class="question-area">
        <div v-if="this.question" class="question-area-canvas">
            <div
                class="undefined"
                v-if="!question[$store.state.homework.questionIndex]"
            >
                <div>
                    <p style="line-height: 526px; font-size: 18pt; color: red;">
                        Bu soru problemli, lütfen öğretmeninize danışınız.
                    </p>
                </div>
            </div>
            <div v-else>
                <div
                    class="type1"
                    v-if="
                        question[$store.state.homework.questionIndex]
                            .question_type === 1
                    "
                >
                    <div class="empties">
                        <div
                            :key="empty.id"
                            :id="'empty' + empty.id"
                            class="empty_rect droparea"
                            :style="{
                                top: coord(empty.bounds)[1] + 'px',
                                left: coord(empty.bounds)[0] + 'px',
                                width: coord(empty.bounds)[2] + 'px',
                                height: coord(empty.bounds)[3] + 'px',
                            }"
                            v-for="empty in question[
                                $store.state.homework.questionIndex
                            ].drop_targets"
                        >
                            <div class="centered" style="width: 100%;">
                                <strong></strong>
                            </div>
                        </div>

                        <button
                            data-toggle="tooltip"
                            title=""
                            v-if="showAnswers"
                            :key="empty.id + 100"
                            :id="'answer' + empty.id"
                            class="answer_block"
                            :style="{
                                top: coord(empty.bounds)[1] + 'px',
                                left: coord(empty.bounds)[0] + 'px',
                                width: coord(empty.bounds)[2] + 'px',
                                height: coord(empty.bounds)[3] + 'px',
                            }"
                            v-for="empty in question[
                                $store.state.homework.questionIndex
                            ].drop_targets"
                        ></button>
                    </div>
                </div>
                <div
                    class="type2"
                    v-if="
                        question[$store.state.homework.questionIndex]
                            .question_type === 2
                    "
                >
                    <div class="lines">
                        <div
                            class="line"
                            :key="line.id"
                            v-for="line in question[
                                $store.state.homework.questionIndex
                            ].drop_targets"
                            :style="{
                                left:
                                    +drawLine(line.bounds, line.anchor)[0] +
                                    'px',
                                top:
                                    +drawLine(line.bounds, line.anchor)[1] +
                                    'px',
                                width:
                                    +drawLine(line.bounds, line.anchor)[2] +
                                    'px',
                                mozTransform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                                webkitTransform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                                oTransform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                                msTransform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                                transform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                            }"
                        ></div>
                    </div>
                    <div class="dots">
                        <div
                            :key="dot.id"
                            class="dot"
                            :style="{
                                top: coord(dot.anchor)[1] + 'px',
                                left: coord(dot.anchor)[0] + 'px',
                            }"
                            v-for="dot in question[
                                $store.state.homework.questionIndex
                            ].drop_targets"
                        ></div>
                    </div>
                    <div class="empties">
                        <div
                            :key="empty.id"
                            :id="'empty' + empty.id"
                            class="empty droparea"
                            :style="{
                                top: coord(empty.bounds)[1] - 20 + 'px',
                                left: coord(empty.bounds)[0] - 20 + 'px',
                            }"
                            v-for="empty in question[
                                $store.state.homework.questionIndex
                            ].drop_targets"
                        ></div>
                        <button
                            data-toggle="tooltip"
                            title=""
                            v-if="showAnswers"
                            :key="empty.id + 100"
                            :id="'answer' + empty.id"
                            class="answer_block"
                            :style="{
                                top: coord(empty.bounds)[1] - 20 + 'px',
                                left: coord(empty.bounds)[0] - 20 + 'px',
                                width: coord(empty.bounds)[2] + 'px',
                                height: coord(empty.bounds)[3] + 'px',
                            }"
                            v-for="empty in question[
                                $store.state.homework.questionIndex
                            ].drop_targets"
                        ></button>
                    </div>
                </div>
                <div
                    class="type3"
                    v-if="
                        question[$store.state.homework.questionIndex]
                            .question_type === 3
                    "
                >
                    <button
                        class="button clear is-primary"
                        @click="showAnswers ? showCorrectMatchings() : clear()"
                    >
                        <span v-if="!showAnswers">RESET</span>
                        <span v-else>GÖSTER</span>
                    </button>
                    <div v-if="this.lines" class="lines">
                        <div
                            class="matching-line"
                            :key="line.id[0] + '' + line.id[1]"
                            :id="line.id[0] + '-' + line.id[1]"
                            v-for="line in this.lines"
                            v-show="!line.hidden"
                            :style="{
                                left:
                                    +drawLine(line.bounds, line.anchor)[0] +
                                    'px',
                                top:
                                    +drawLine(line.bounds, line.anchor)[1] +
                                    'px',
                                width:
                                    +drawLine(line.bounds, line.anchor)[2] +
                                    'px',
                                mozTransform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                                webkitTransform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                                oTransform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                                msTransform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                                transform:
                                    'rotate(' +
                                    drawLine(line.bounds, line.anchor)[3] +
                                    'deg)',
                                backgroundColor: line.color,
                            }"
                        ></div>
                    </div>
                    <div class="matchers">
                        <button
                            @click="matcher(match.id)"
                            :key="match.id"
                            :id="'match' + match.id"
                            :data-coord="
                                '{x:' +
                                coord(match.bounds)[0] +
                                ',y:' +
                                coord(match.bounds)[1] +
                                '}'
                            "
                            class="match"
                            :style="{
                                top: coord(match.bounds)[1] + 'px',
                                left: coord(match.bounds)[0] + 'px',
                                width: coord(match.bounds)[2] - 10 + 'px',
                                height: coord(match.bounds)[3] - 10 + 'px',
                            }"
                            v-for="match in question[
                                $store.state.homework.questionIndex
                            ].drop_targets"
                        ></button>
                    </div>
                    <div id="mova" style="position: absolute; z-index: 100;">
                        &nbsp;
                    </div>
                    <div id="movb" style="position: absolute; z-index: 100;">
                        &nbsp;
                    </div>
                </div>
                <div
                    class="type4"
                    v-if="
                        question[$store.state.homework.questionIndex]
                            .question_type === 4
                    "
                >
                    <div class="checks">
                        <div
                            :key="check.id"
                            class="check_area"
                            v-for="check in question[
                                $store.state.homework.questionIndex
                            ].drop_targets"
                        >
                            <button
                                class="check"
                                :id="'check' + check.id"
                                :class="[
                                    { checked: check.checked },
                                    { unchecked: !check.checked },
                                ]"
                                @click="check.checked = !check.checked"
                                :style="{
                                    top: coord(check.bounds)[1] + 'px',
                                    left: coord(check.bounds)[0] + 'px',
                                    width: coord(check.bounds)[2] + 'px',
                                    height: coord(check.bounds)[3] + 'px',
                                }"
                            ></button>
                        </div>
                    </div>
                </div>
                <div class="child">
                    <img
                        class="img"
                        draggable="false"
                        :style="{
                            top: coord(image.bounds)[1] + 'px',
                            left: coord(image.bounds)[0] + 'px',
                            width: coord(image.bounds)[2] + 'px',
                            height: coord(image.bounds)[3] + 'px',
                        }"
                        :key="image.id"
                        :src="image.image_url"
                        v-for="image in question[
                            $store.state.homework.questionIndex
                        ].images"
                        alt="Bu sorunun görseli yok :("
                    />
                    <a
                        v-if="
                            question[$store.state.homework.questionIndex].id ===
                            info.question_id
                        "
                        :href="info.inf_url"
                        class="info"
                        :style="{
                            top: coord(info.bounds)[1] + 'px',
                            left: coord(info.bounds)[0] + 'px',
                            wifdth: coord(info.bounds)[2] + 'px',
                            height: coord(info.bounds)[3] + 'px',
                        }"
                        :key="info.id"
                        v-for="info in this.information.file.office_files"
                        download
                    ></a>
                    <a
                        v-if="
                            question[$store.state.homework.questionIndex].id ===
                            info.question_id
                        "
                        :href="info.inf_url"
                        class="info"
                        target="_blank"
                        :style="{
                            top: coord(info.bounds)[1] + 'px',
                            left: coord(info.bounds)[0] + 'px',
                            width: coord(info.bounds)[2] + 'px',
                            height: coord(info.bounds)[3] + 'px',
                        }"
                        :key="info.id"
                        v-for="info in this.information.website"
                    ></a>
                    <button
                        v-if="
                            question[$store.state.homework.questionIndex].id ===
                            info.question_id
                        "
                        @click="showInfo(info.id, 'text')"
                        class="info"
                        :style="{
                            top: coord(info.bounds)[1] + 'px',
                            left: coord(info.bounds)[0] + 'px',
                            width: coord(info.bounds)[2] + 'px',
                            height: coord(info.bounds)[3] + 'px',
                        }"
                        :key="info.id"
                        v-for="info in this.information.text"
                    ></button>
                    <button
                        v-if="
                            question[$store.state.homework.questionIndex].id ===
                            info.question_id
                        "
                        @click="showInfo(info.id, 'audio')"
                        class="info"
                        :style="{
                            top: coord(info.bounds)[1] + 'px',
                            left: coord(info.bounds)[0] + 'px',
                            width: coord(info.bounds)[2] + 'px',
                            height: coord(info.bounds)[3] + 'px',
                        }"
                        :key="info.id"
                        v-for="info in this.information.file.audio"
                    ></button>
                    <button
                        v-if="
                            question[$store.state.homework.questionIndex].id ===
                            info.question_id
                        "
                        @click="showInfo(info.id, 'image')"
                        class="info"
                        :style="{
                            top: coord(info.bounds)[1] + 'px',
                            left: coord(info.bounds)[0] + 'px',
                            width: coord(info.bounds)[2] + 'px',
                            height: coord(info.bounds)[3] + 'px',
                        }"
                        :key="info.id"
                        v-for="info in this.information.file.image"
                    ></button>
                    <button
                        v-if="
                            question[$store.state.homework.questionIndex].id ===
                            info.question_id
                        "
                        @click="showInfo(info.id, 'video')"
                        class="info"
                        :style="{
                            top: coord(info.bounds)[1] + 'px',
                            left: coord(info.bounds)[0] + 'px',
                            width: coord(info.bounds)[2] + 'px',
                            height: coord(info.bounds)[3] + 'px',
                        }"
                        :key="info.id"
                        v-for="info in this.information.file.video"
                    ></button>
                    <button
                        v-if="
                            question[$store.state.homework.questionIndex].id ===
                            info.question_id
                        "
                        @click="showInfo(info.id, 'embed')"
                        class="info"
                        :style="{
                            top: coord(info.bounds)[1] + 'px',
                            left: coord(info.bounds)[0] + 'px',
                            width: coord(info.bounds)[2] + 'px',
                            height: coord(info.bounds)[3] + 'px',
                        }"
                        :key="info.id"
                        v-for="info in this.information.embed"
                    ></button>
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
        <div class="modal info-modal-video">
            <div
                class="modal-background"
                @click="toggleModal('.info-modal-video')"
            ></div>
            <div>
                <header class="modal-card-head">
                    <p class="modal-card-title">Info</p>
                    <button
                        class="delete"
                        @click="toggleModal('.info-modal-video')"
                        aria-label="close"
                    ></button>
                </header>
                <vue-plyr :key="video_source">
                    <video style="height: 85vh;">
                        <source :src="video_source" />
                    </video>
                </vue-plyr>
            </div>
        </div>
        <div class="modal info-modal-audio">
            <div
                class="modal-background"
                @click="toggleModal('.info-modal-audio')"
            ></div>
            <div style="width: 40vw;">
                <header class="modal-card-head">
                    <p class="modal-card-title">Info</p>
                    <button
                        class="delete"
                        @click="toggleModal('.info-modal-audio')"
                        aria-label="close"
                    ></button>
                </header>
                <vue-plyr :key="audio_source">
                    <audio>
                        <source :src="audio_source" />
                    </audio>
                </vue-plyr>
            </div>
        </div>
        <div class="modal info-modal-embed">
            <div
                class="modal-background"
                @click="toggleModal('.info-modal-embed')"
            ></div>
            <div style="width: 40vw;">
                <header class="modal-card-head">
                    <p class="modal-card-title">Info</p>
                    <button
                        class="delete"
                        @click="toggleModal('.info-modal-embed')"
                        aria-label="close"
                    ></button>
                </header>
                <vue-plyr :key="embed_source">
                    <div class="plyr__video-embed">
                        <iframe :src="embed_source"> </iframe>
                    </div>
                </vue-plyr>
            </div>
        </div>
        <div class="modal info-modal-image">
            <div
                class="modal-background"
                @click="toggleModal('.info-modal-image')"
            ></div>
            <div>
                <header class="modal-card-head">
                    <p class="modal-card-title">Info</p>
                    <button
                        class="delete"
                        @click="toggleModal('.info-modal-image')"
                        aria-label="close"
                    ></button>
                </header>
                <div style="position: sticky;">
                    <img style="height: 85vh;" :src="image_source" alt="" />
                </div>
            </div>
        </div>
        <div class="modal info-modal-text">
            <div
                class="modal-background"
                @click="toggleModal('.info-modal-text')"
            ></div>
            <div>
                <header class="modal-card-head">
                    <p class="modal-card-title">Info</p>
                    <button
                        class="delete"
                        @click="toggleModal('.info-modal-text')"
                        aria-label="close"
                    ></button>
                </header>
                <div
                    style="
                        position: sticky;
                        background-color: white;
                        padding: 20px;
                        height: auto;
                        width: 40vw;
                    "
                >
                    <p class="info-text"></p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import StudentService from "../../services/student.service";
import { mapActions, mapGetters } from "vuex";
import TweenLite from "gsap";
import "html2canvas";
import "canvas-toBlob";
import html2canvas from "html2canvas";

export default {
    name: "questionArea",
    props: ["jsonQuestions", "isLoaded", "preQuestion", "isShown"],
    data() {
        return {
            question: this.jsonQuestions,
            answer_model: {
                option: 3,
                question_index: 1,
                option_matched: true,
                time: 2,
            },
            answers: {},
            targets: {},
            checkboxes: [],
            temporary_target: 0,
            lines: [],
            correct: false,
            information: {
                file: {
                    audio: [],
                    image: [],
                    video: [],
                    office_files: [],
                },
                website: [],
                text: [],
                embed: [],
            },
            audio: ["mp3", "ogg", "wav"],
            image: ["ai", "gif", "bmp", "ico", "jpeg", "jpg", "png", "svg"],
            video: [
                "3gp",
                "avi",
                "flv",
                "h264",
                "m4v",
                "mkv",
                "mov",
                "mp4",
                "mpg",
                "mpeg",
                "swf",
                "wmv",
                "webm",
            ],
            office_files: [
                "xlsx",
                "xlsm",
                "xls",
                "ods",
                "xml",
                "doc",
                "docx",
                "odt",
                "txt",
                "pptx",
                "ppt",
                "pps",
            ],
            image_source: "",
            audio_source: "",
            embed_source: "",
            video_source: "",
        };
    },
    methods: {
        coord(coordinates) {
            return coordinates.replace("{", "").replace("}", "").split(",");
        },
        drawLine(coords, coords2) {
            // bottom right
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 3
            ) {
                var x1 = Number(this.coord(coords)[0]) + 11;
                var y1 = Number(this.coord(coords)[1]) + 11;
                // top right
                var x2 = Number(this.coord(coords2)[0]) + 11;
                var y2 = Number(this.coord(coords2)[1]) + 11;
            } else {
                var x1 = Number(this.coord(coords)[0]);
                var y1 = Number(this.coord(coords)[1]);
                // top right
                var x2 = Number(this.coord(coords2)[0]) + 4;
                var y2 = Number(this.coord(coords2)[1]) + 4;
            }
            // distance
            var length = Math.sqrt(
                (x2 - x1) * (x2 - x1) + (y2 - y1) * (y2 - y1)
            );
            // center
            var cx = (x1 + x2) / 2 - length / 2;
            var cy = (y1 + y2) / 2 - 2 / 2;

            var angle = Math.atan2(y1 - y2, x1 - x2) * (180 / Math.PI);

            return [cx, cy, length, angle];
            //
        },
        matcher(id) {
            debugger;
            let same_line = true;
            if (this.temporary_target === 0) {
                this.temporary_target = id;
            } else {
                if (typeof this.targets["match" + id] !== "undefined") {
                    if (
                        this.targets["match" + id].includes(
                            "match" + this.temporary_target
                        )
                    ) {
                        same_line = false;
                    }
                }
                if (
                    typeof this.targets["match" + this.temporary_target] !==
                    "undefined"
                ) {
                    if (
                        this.targets["match" + this.temporary_target].includes(
                            "match" + id
                        )
                    ) {
                        same_line = false;
                    }
                }
                if (this.temporary_target === id || !same_line) {
                    this.temporary_target = 0;
                } else {
                    if (this.targets["match" + id]) {
                        this.targets["match" + id].push(
                            "match" + this.temporary_target
                        );
                        this.lines.push({
                            id: [id, this.temporary_target],
                            bounds: document
                                .querySelector("#match" + id)
                                .dataset.coord.replace("y:", "")
                                .replace("x:", ""),
                            anchor: document
                                .querySelector("#match" + this.temporary_target)
                                .dataset.coord.replace("y:", "")
                                .replace("x:", ""),
                            correctAnswer: 0,
                            color: "black",
                            hidden: 0,
                            original: 1,
                        });
                        this.temporary_target = 0;
                    } else {
                        this.targets["match" + id] = [];
                        this.targets["match" + id].push(
                            "match" + this.temporary_target
                        );
                        this.lines.push({
                            id: [id, this.temporary_target],
                            bounds: document
                                .querySelector("#match" + id)
                                .dataset.coord.replace("y:", "")
                                .replace("x:", ""),
                            anchor: document
                                .querySelector("#match" + this.temporary_target)
                                .dataset.coord.replace("y:", "")
                                .replace("x:", ""),
                            correctAnswer: 0,
                            color: "black",
                            hidden: 0,
                            original: 1,
                        });
                        this.temporary_target = 0;
                    }
                }
            }
        },
        clear() {
            this.targets = {};
            this.lines = [];
            this.temporary_target = 0;
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
            document
                .querySelector(".question-area-canvas")
                .classList.add("resetted");
        },
        showCorrectMatchings() {
            if (this.lines.find((element) => element.original)) {
                if (this.lines.find((element) => element.original).hidden) {
                    this.lines.forEach((element) => {
                        element.original
                            ? (element.hidden = 0)
                            : (element.hidden = 1);
                    });
                } else {
                    this.lines.forEach((element) => {
                        element.correctAnswer
                            ? (element.hidden = 0)
                            : (element.hidden = 1);
                    });
                }
            } else {
                this.lines.forEach((element) => {
                    element.hidden = !element.hidden;
                });
            }
        },
        async setData(question_id) {
            let questionResult = [];

            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 3
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

                for (let i = 0; i < this.lines.length; i++) {
                    let line = this.lines[i];
                    let model = {};
                    model.option = line.id[0];
                    model.option_matched = line.id[1];
                    model.time = time;
                    model.question_id = question_id;
                    questionResult.push(model);
                }
                questionResult = questionResult.length
                    ? questionResult
                    : [{ time: time, question_id: question_id }];
            }

            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 4
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
                for (let i = 0; i < this.checkboxes.length; i++) {
                    this.checkboxes[i].drop_targets.forEach((check) => {
                        if (check.checked === true) {
                            let model = {};
                            model.option = check.id;
                            model.option_matched = true;
                            model.time = time;
                            model.question_id = question_id;
                            questionResult.push(model);
                        }
                    });

                    questionResult = questionResult.length
                        ? questionResult
                        : [{ time: time, question_id: question_id }];
                }
            }

            await StudentService.UpdateAllQuestionResult(
                this.$store.state.homework.homeworkId,
                question_id,
                questionResult
            );
            await this.setStudentAnswer(questionResult);
        },
        async getData(i) {
            setTimeout(this.True);
            await StudentService.GetAllQuestionResult(
                this.$store.state.homework.homeworkId,
                i
            ).then((res) => {
                this.answers = [...res.data];
                this.clear();
                if (this.answers.length > 0) {
                    this.useData();
                } else {
                    if (
                        this.showAnswers &&
                        this.question[this.$store.state.homework.questionIndex]
                            .question_type === 3 &&
                        !this.question[this.$store.state.homework.questionIndex]
                            .isPlaced
                    ) {
                        this.placeAnswer();
                        this.question[
                            this.$store.state.homework.questionIndex
                        ].isPlaced = 1;
                    }
                }
                if (
                    this.showAnswers &&
                    this.question[this.$store.state.homework.questionIndex]
                        .question_type !== 3
                ) {
                    this.placeAnswer();
                }
                this.$store.commit("homework/toggleShow");
            });
        },
        useData() {
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 3
            ) {
                for (let i = 0; i < this.answers.length; i++) {
                    this.matcher(this.answers[i].option);
                    this.matcher(this.answers[i].option_matched);
                }
                if (
                    this.showAnswers &&
                    !this.question[this.$store.state.homework.questionIndex]
                        .isPlaced
                ) {
                    const self = this;
                    setTimeout(function () {
                        self.placeAnswer();
                        self.question[
                            self.$store.state.homework.questionIndex
                        ].isPlaced = 1;
                    });
                }
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 4
            ) {
                for (let i = 0; i < this.answers.length; i++) {
                    console.log(
                        this.question[this.$store.state.homework.questionIndex]
                            .drop_targets
                    );
                    if (
                        _.find(
                            this.question[
                                this.$store.state.homework.questionIndex
                            ].drop_targets,
                            (checkbox) => {
                                return (
                                    checkbox.id ===
                                    Number(this.answers[i].option)
                                );
                            }
                        )
                    ) {
                        _.find(
                            this.question[
                                this.$store.state.homework.questionIndex
                            ].drop_targets,
                            (checkbox) => {
                                return (
                                    checkbox.id ===
                                    Number(this.answers[i].option)
                                );
                            }
                        ).checked = true;
                    }
                }
            }
        },
        True() {
            this.correct = true;
        },
        informationCategorizer() {
            this.question.forEach((element) => {
                if (element.information.length > 0) {
                    element.information.forEach((el) => {
                        if (
                            (!el.visible_before &&
                                this.$store.state.homework.isDone) ||
                            el.visible_before
                        ) {
                            if (el.inf_type === 1) {
                                const file_type = el.inf_url
                                    .split(".")
                                    [
                                        el.inf_url.split(".").length - 1
                                    ].toLowerCase();
                                if (this.audio.includes(file_type)) {
                                    this.information.file.audio.push(el);
                                }
                                if (this.video.includes(file_type)) {
                                    this.information.file.video.push(el);
                                }
                                if (this.office_files.includes(file_type)) {
                                    this.information.file.office_files.push(el);
                                }
                                if (this.image.includes(file_type)) {
                                    this.information.file.image.push(el);
                                }
                                if (file_type === "pdf") {
                                    this.information.website.push(el);
                                }
                            }
                            if (el.inf_type === 2) {
                                if (!el.text.includes("youtube")) {
                                    this.information.website.push(el);
                                } else {
                                    this.information.embed.push(el);
                                }
                            }
                            if (el.inf_type === 3) {
                                this.information.text.push(el);
                            }
                        }
                    });
                }
            });
        },
        toggleModal(className) {
            if (
                document
                    .querySelector(className)
                    .classList.contains("is-active")
            ) {
                document.querySelector(className).classList.remove("is-active");
                this.audio_source = "";
                this.embed_source = "";
                this.image_source = "";
                this.video_source = "";
            } else {
                document.querySelector(className).classList.add("is-active");
            }
        },
        showInfo(id, type) {
            if ("audio" === type) {
                this.audio_source = _.find(
                    this.information.file.audio,
                    (info) => {
                        return info.id === id;
                    }
                ).inf_url;
                this.toggleModal(".info-modal-audio");
            }
            if ("video" === type) {
                this.video_source = _.find(
                    this.information.file.video,
                    (info) => {
                        return info.id === id;
                    }
                ).inf_url;
                this.toggleModal(".info-modal-video");
            }
            if ("embed" === type) {
                this.embed_source = _.find(this.information.embed, (info) => {
                    return info.id === id;
                }).text;
                this.toggleModal(".info-modal-embed");
            }
            if ("image" === type) {
                this.image_source = _.find(
                    this.information.file.image,
                    (info) => {
                        return info.id === id;
                    }
                ).inf_url;
                this.toggleModal(".info-modal-image");
            }
            if ("text" === type) {
                document.querySelector(".info-text ").innerHTML = _.find(
                    this.information.text,
                    (info) => {
                        return info.id === id;
                    }
                ).text;
                this.toggleModal(".info-modal-text");
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
                    .question_type === 4
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
                                    correctAnswer.drop_targets[i].checked
                                ) === student_answer.option_matched
                            ) {
                                TweenLite.set(
                                    document.querySelector(
                                        "#check" +
                                            correctAnswer.drop_targets[i].id
                                    ),
                                    {
                                        boxShadow: "0px 0px 0px 3px green",
                                        borderRadius: "16px",
                                    }
                                );
                            } else {
                                TweenLite.set(
                                    document.querySelector(
                                        "#check" +
                                            correctAnswer.drop_targets[i].id
                                    ),
                                    {
                                        boxShadow: "0px 0px 0px 3px red",
                                        borderRadius: "16px",
                                    }
                                );
                            }
                        } else {
                            if (correctAnswer.drop_targets[i].checked) {
                                TweenLite.set(
                                    document.querySelector(
                                        "#check" +
                                            correctAnswer.drop_targets[i].id
                                    ),
                                    {
                                        boxShadow: "0px 0px 0px 3px red",
                                        borderRadius: "16px",
                                    }
                                );
                            }
                        }
                    }
                }
            }
            if (
                this.question[this.$store.state.homework.questionIndex]
                    .question_type === 3
            ) {
                if (this.answers.length === 0) {
                    correctAnswer.target_matchings.forEach((item) => {
                        let model = {
                            bounds: document
                                .querySelector(
                                    "#match" + item.destination_drop_id
                                )
                                .dataset.coord.replace("y:", "")
                                .replace("x:", ""),
                            anchor: document
                                .querySelector("#match" + item.source_drop_id)
                                .dataset.coord.replace("y:", "")
                                .replace("x:", ""),
                            id: [item.destination_drop_id, item.source_drop_id],
                            correctAnswer: 1,
                            color: "green",
                            hidden: 1,
                            original: 0,
                        };
                        this.lines.push(model);
                    });
                } else {
                    let corrects = [];

                    correctAnswer.target_matchings.forEach((element) => {
                        let model = {};
                        model.id = String(element.source_drop_id);
                        model.matched = [String(element.destination_drop_id)];
                        corrects.push(model);
                    });
                    correctAnswer.target_matchings.forEach((element) => {
                        let model = {};
                        model.id = String(element.destination_drop_id);
                        model.matched = [String(element.source_drop_id)];
                        corrects.push(model);
                    });

                    const arrayHashmap = corrects.reduce((obj, item) => {
                        obj[item.id]
                            ? obj[item.id].matched.push(...item.matched)
                            : (obj[item.id] = { ...item });
                        return obj;
                    }, {});

                    corrects = Object.values(arrayHashmap);
                    let answers = [];
                    this.answers.forEach((element) => {
                        let model = {};
                        model.id = element.option;
                        model.matched = [element.option_matched];
                        answers.push(model);
                    });
                    this.answers.forEach((element) => {
                        let model = {};
                        model.id = element.option_matched;
                        model.matched = [element.option];
                        answers.push(model);
                    });

                    const newArrayHashmap = answers.reduce((obj, item) => {
                        obj[item.id]
                            ? obj[item.id].matched.push(...item.matched)
                            : (obj[item.id] = { ...item });
                        return obj;
                    }, {});

                    answers = Object.values(newArrayHashmap);

                    this.answers.forEach((element) => {
                        let correct_matches = corrects.find(function (match) {
                            if (String(match.id) === element.option) {
                                return match.matched;
                            }
                        });
                        if (
                            correct_matches.matched.includes(
                                element.option_matched
                            )
                        ) {
                            const line = this.lines.find(function (match) {
                                if (
                                    _.isEmpty(
                                        _.xor(match.id, [
                                            element.option_matched,
                                            element.option,
                                        ])
                                    )
                                ) {
                                    return match;
                                }
                            });
                            line.color = "green";
                            line.correctAnswer = 1;
                        } else {
                            const line = this.lines.find(function (match) {
                                if (
                                    _.isEmpty(
                                        _.xor(match.id, [
                                            element.option_matched,
                                            element.option,
                                        ])
                                    )
                                ) {
                                    return match;
                                }
                            });
                            line.color = "red";
                        }
                    });
                    corrects.forEach((element) => {
                        let correct_matches = answers.find(function (match) {
                            if (match.id === String(element.id)) {
                                return match.matched;
                            }
                        });
                        if (!correct_matches) {
                            correct_matches = {
                                matched: [],
                            };
                        }

                        let difference = element.matched.filter(
                            (x) => !correct_matches.matched.includes(x)
                        );
                        difference.forEach((item) => {
                            let model = {
                                bounds: document
                                    .querySelector("#match" + element.id)
                                    .dataset.coord.replace("y:", "")
                                    .replace("x:", ""),
                                anchor: document
                                    .querySelector("#match" + item)
                                    .dataset.coord.replace("y:", "")
                                    .replace("x:", ""),
                                id: [element.id, item],
                                correctAnswer: 1,
                                color: "green",
                                hidden: 1,
                                original: 0,
                            };
                            this.lines.push(model);
                        });
                    });
                }
            }
        },
        ...mapActions({
            setStudentAnswer: "homework/setStudentAnswer",
        }),
    },
    computed: {
        ...mapGetters({
            correctAnswers: "homework/answers",
            showAnswers: "homework/showAnswers",
        }),
    },
    watch: {
        // whenever question changes, this function will run
        jsonQuestions: function () {
            this.question = this.jsonQuestions.data.questions;

            if ([3, 4].includes(this.question[0].question_type)) {
                this.getData(this.question[0].id);
            }
            this.question.forEach((question) => {
                if (question.question_type === 4) {
                    this.checkboxes.push({
                        id: question.id,
                        drop_targets: question.drop_targets,
                    });
                }
            });

            for (let i = 0; i < this.checkboxes.length; i++) {
                this.checkboxes[i].drop_targets.forEach((element) => {
                    element.checked = 0;
                });
            }
            this.informationCategorizer();
        },
        "$store.state.homework.question_question_area": function () {
            this.getData(
                this.question[this.$store.state.homework.questionIndex].id
            );

            setTimeout(this.True);
        },
        preQuestion: function () {
            if (
                (this.question[this.$store.state.homework.questionIndex]
                    .question_type === 3 ||
                    this.question[this.$store.state.homework.questionIndex]
                        .question_type === 4) &&
                this.correct
            ) {
                if (!this.$store.state.homework.isDone) {
                    this.setData(
                        this.question[this.$store.state.homework.questionIndex]
                            .id
                    );
                }
                this.correct = false;
                if (this.preQuestion.includes("prev")) {
                    this.$emit("prev-question");
                }
                if (this.preQuestion.includes("next")) {
                    this.$emit("next-question");
                }
                if (
                    !(
                        this.question[this.$store.state.homework.questionIndex]
                            .question_type === 3 ||
                        this.question[this.$store.state.homework.questionIndex]
                            .question_type === 4
                    )
                ) {
                    this.$store.commit("homework/callAnswerArea");
                } else {
                    this.getData(
                        this.question[this.$store.state.homework.questionIndex]
                            .id
                    );
                }
            }
        },
    },
};
</script>

<style scoped>
button {
    border: none;
    z-index: 1;
}
.clear {
    position: absolute;
    right: 0;
    bottom: 0;
    border-radius: 6px;
    z-index: 39;
}
.match {
    background: url("/images/liconlar/V2MatchingOptionDraggable.png") no-repeat;
    position: absolute;
    z-index: 1;
    cursor: pointer;
}
canvas {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1;
}
.check {
    position: absolute;
    transition: background-image 200ms linear;
}
svg {
    position: absolute;
    z-index: 1;
    top: 0;
    left: 0;
}

.centered {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.unchecked {
    background: url("/images/GridUnchecked.png") no-repeat;
}
.checked {
    background: url("/images/GridChecked.png") no-repeat;
}

.line {
    line-height: 1px;
    position: absolute;
    padding: 0;
    margin: 0;
    background-color: black;
    height: 2px;
    z-index: 3;
}
.matching-line {
    line-height: 1px;
    position: absolute;
    padding: 0;
    margin: 0;
    background-color: black;
    height: 3px;
    z-index: 1;
}
.empty {
    background: url("/images/liconlar/matching_option_image_empty.png")
        no-repeat;
    position: absolute;
    height: 45px;
    width: 200px;
    z-index: 4;
}
.empty_rect {
    position: absolute;
    z-index: 4;
    border: 2px solid black;
}
.answer_block {
    position: absolute;
    z-index: 39;
    background-color: rgba(0, 0, 0, 0);
}

.child .img {
    position: absolute;
}
.question-area {
    border: 2px solid gray;
    position: relative;
    background-color: #f2f2f2;
    border-right: 3px solid gray;
}
.question-area-canvas {
    width: 569.9px;
    height: 530px;
    border: 2px darkgrey solid;
    border-radius: 9px;
    position: absolute;
    background-color: white;
    top: 20px;
    left: 10px;
    text-align: center;
}

.dot {
    position: absolute;
    top: 233px;
    left: 269px;
    width: 8px;
    height: 8px;
    background-color: black;
    border-radius: 8px;
    z-index: 2;
}
.info {
    background: url("/images/liconlar/EditorObjectInfo.png") no-repeat;
    position: absolute;
    z-index: 39;
}
.delete::after {
    height: 80%;
}
.delete::before {
    width: 80%;
}
.delete {
    background-color: rgba(10, 10, 10, 0);
}
.modal-card-head {
    background-color: rgba(198, 198, 198, 0.07);
    border-bottom: none;
}
.modal-card-title {
    color: #f2f2f2;
}
strong {
    color: #5679b3;
}
</style>
