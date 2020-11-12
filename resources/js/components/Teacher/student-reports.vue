<template>
    <div>
        <button
            class="button is-success back"
            @click="goBack"
            v-show="stage > 1"
        >
            GERİ
        </button>
        <vue-good-table
            :key="1"
            v-if="getUser === 1 && getTab === 0 && stage === 1"
            :columns="columns"
            :rows="rows"
            @on-row-click="formatTable"
            @on-page-change="onPageChange"
            @on-sort-change="onSortChange"
            @on-column-filter="onColumnFilter"
            :class="{ 'padding-top-120': stage < 2 }"
            :isLoading.sync="loading"
            :pagination-options="{
                enabled: true,
                mode: 'pages',
                perPage: 20,
                perPageDropdown: [20],
                dropdownAllowAll: false,
                nextLabel: 'sonraki',
                prevLabel: 'önceki',
                rowsPerPageLabel: 'Sayfa başı satır sayısı',
                ofLabel: '',
                pageLabel: 'sayfa', // for 'pages' mode
            }"
            mode="remote"
            :totalRows="totalRecords"
            styleClass="vgt-table condensed striped bordered"
            class="main-table"
            ><div slot="emptystate">Bu kategoride henüz bir veri yok.</div>
            <template slot="table-row" slot-scope="props">
                <span v-if="props.column.field === 'comment'">
                    <button
                        class="comment-button button is-success"
                        @click="comment(props.row)"
                    >
                        Yorum yap
                    </button>
                </span>
                <span
                    v-else-if="
                        props.column.field === 'grade' &&
                        props.row.type === 'Açık Uçlu Soru' &&
                        !props.row.isGraded
                    "
                >
                    <button
                        class="grade-button button is-success"
                        @click="grade(props.row)"
                    >
                        Not ver
                    </button>
                </span>
                <span
                    v-else-if="
                        props.column.field === 'grade' &&
                        props.row.type === 'Açık Uçlu Soru' &&
                        props.row.isGraded
                    "
                >
                    <button class="grade-button button is-success" disabled>
                        Not verildi
                    </button>
                </span>
                <span
                    v-else-if="
                        (props.column.field === 'answer_image' &&
                            props.row.answer_image === undefined) ||
                        (props.column.field === 'answer_audio' &&
                            props.row.answer_audio === undefined)
                    "
                >
                    <img
                        src="images/liconlar/loading.svg"
                        style="width: 30px"
                        alt=""
                    />
                </span>
                <span
                    v-else-if="
                        props.column.field === 'answer_image' &&
                        props.row.answer_image
                    "
                >
                    <button
                        class="image-button button is-info"
                        @click="showScreenshot(props.row)"
                    ></button>
                </span>
                <span
                    v-else-if="
                        props.column.field === 'answer_audio' &&
                        props.row.answer_audio
                    "
                >
                    <button
                        class="sound-button button is-warning"
                        @click="showAudio(props.row)"
                    ></button>
                </span>

                <span v-else-if="props.column.field === 'delete'">
                    <button
                        class="delete-button button is-danger"
                        @click="deleteHomework(props.row)"
                    >
                        Ödevi Sil
                    </button>
                </span>
                <span
                    v-else-if="
                        (props.column.field === 'score' &&
                            props.row.isSubmitBool) ||
                        props.column.type === 'percentage'
                    "
                >
                    <div class="bg">
                        <div
                            class="html"
                            :style="{
                                width: props.formattedRow[props.column.field],
                            }"
                        ></div>
                        <span class="percentage">{{
                            props.formattedRow[props.column.field]
                        }}</span>
                    </div> </span
                ><span
                    v-else-if="
                        props.column.field === 'deadline' && getTab === 0
                    "
                >
                    <span
                        v-if="
                            !pickDateTime ||
                            !(props.row.originalIndex === deadlineIndex)
                        "
                        ><span>{{
                            props.formattedRow[props.column.field]
                        }}</span>
                        <button
                            class="edit-button button is-warning"
                            @click="edit(props.row)"
                        ></button
                    ></span>
                    <span v-else>
                        <datetime
                            zone="UTC+3"
                            :min-datetime="minDatetime"
                            type="datetime"
                            v-model="deadline"
                        ></datetime>
                        <button
                            class="confirm-button button is-success"
                            @click="confirmEdit"
                        ></button
                        ><button
                            class="cancel-button button is-danger"
                            @click="cancel"
                        ></button>
                    </span>
                </span>
                <span
                    v-else-if="
                        props.column.field === 'isSubmit' &&
                        compareDates(
                            props.row.isSubmit,
                            chosen_homework.deadline
                        )
                    "
                    style="color: red"
                >
                    {{ props.formattedRow[props.column.field] }}
                </span>
                <span v-else>
                    {{ props.formattedRow[props.column.field] }}
                </span>
            </template>
        </vue-good-table>

        <vue-good-table
            :key="2"
            :columns="columns"
            :rows="rows"
            :class="{ 'padding-top-120': stage < 2 }"
            :isLoading="loading"
            @on-row-click="formatTable"
            :sort-options="{
                enabled: false,
            }"
            v-else
            styleClass="vgt-table condensed striped bordered"
            class="main-table"
            ><div slot="emptystate">Bu kategoride henüz bir veri yok.</div>
            <template slot="table-row" slot-scope="props">
                <span v-if="props.column.field === 'comment'">
                    <button
                        class="comment-button button is-success"
                        @click="comment(props.row)"
                    >
                        Yorum yap
                    </button>
                </span>
                <span
                    v-else-if="
                        props.column.field === 'grade' &&
                        props.row.type === 'Açık Uçlu Soru' &&
                        !props.row.isGraded
                    "
                >
                    <button
                        class="grade-button button is-success"
                        @click="grade(props.row)"
                    >
                        Not ver
                    </button>
                </span>
                <span
                    v-else-if="
                        props.column.field === 'grade' &&
                        props.row.type === 'Açık Uçlu Soru' &&
                        props.row.isGraded
                    "
                >
                    <button class="grade-button button is-success" disabled>
                        Not verildi
                    </button>
                </span>
                <span
                    v-else-if="
                        (props.column.field === 'answer_image' &&
                            props.row.answer_image === undefined) ||
                        (props.column.field === 'answer_audio' &&
                            props.row.answer_audio === undefined)
                    "
                >
                    <img
                        src="images/liconlar/loading.svg"
                        style="width: 30px"
                        alt=""
                    />
                </span>
                <span
                    v-else-if="
                        props.column.field === 'answer_image' &&
                        props.row.answer_image
                    "
                >
                    <button
                        class="image-button button is-info"
                        @click="showScreenshot(props.row)"
                    ></button>
                </span>
                <span
                    v-else-if="
                        props.column.field === 'answer_audio' &&
                        props.row.answer_audio
                    "
                >
                    <button
                        class="sound-button button is-warning"
                        @click="showAudio(props.row)"
                    ></button>
                </span>

                <span v-else-if="props.column.field === 'delete'">
                    <button
                        class="delete-button button is-danger"
                        @click="deleteHomework(props.row)"
                    >
                        Ödevi Sil
                    </button>
                </span>
                <span
                    v-else-if="
                        (props.column.field === 'score' &&
                            props.row.isSubmitBool) ||
                        props.column.type === 'percentage'
                    "
                >
                    <div class="bg">
                        <div
                            class="html"
                            :style="{
                                width: props.formattedRow[props.column.field],
                            }"
                        ></div>
                        <span class="percentage">{{
                            props.formattedRow[props.column.field]
                        }}</span>
                    </div> </span
                ><span
                    v-else-if="
                        props.column.field === 'deadline' && getTab === 0
                    "
                >
                    <span
                        v-if="
                            !pickDateTime ||
                            !(props.row.originalIndex === deadlineIndex)
                        "
                        ><span>{{
                            props.formattedRow[props.column.field]
                        }}</span>
                        <button
                            class="edit-button button is-warning"
                            @click="edit(props.row)"
                        ></button
                    ></span>
                    <span v-else>
                        <datetime
                            zone="UTC+3"
                            :min-datetime="minDatetime"
                            type="datetime"
                            v-model="deadline"
                        ></datetime>
                        <button
                            class="confirm-button button is-success"
                            @click="confirmEdit"
                        ></button
                        ><button
                            class="cancel-button button is-danger"
                            @click="cancel"
                        ></button>
                    </span>
                </span>
                <span
                    v-else-if="
                        props.column.field === 'isSubmit' &&
                        compareDates(
                            props.row.isSubmit,
                            chosen_homework.deadline
                        )
                    "
                    style="color: red"
                >
                    {{ props.formattedRow[props.column.field] }}
                </span>
                <span v-else>
                    {{ props.formattedRow[props.column.field] }}
                </span>
            </template>
        </vue-good-table>
        <div class="modal comment-modal">
            <div
                class="modal-background"
                @click="toggleModal('.comment-modal')"
            ></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Yorum yap</p>
                    <button
                        class="delete"
                        aria-label="close"
                        @click="toggleModal('.comment-modal')"
                    ></button>
                </header>
                <section class="modal-card-body">
                    <div>
                        <textarea name="comment" id="comment"></textarea>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button class="button is-success" @click="postComment">
                        Kaydet
                    </button>
                    <button
                        class="button"
                        @click="toggleModal('.comment-modal')"
                    >
                        Çık
                    </button>
                </footer>
            </div>
        </div>

        <div class="modal grade-modal">
            <div
                class="modal-background"
                @click="toggleModal('.grade-modal')"
            ></div>
            <div class="modal-card">
                <section class="modal-card-body">
                    <div class="answer-block">
                        <p class="answer-header">Öğrencinin cevabı:</p>
                        <p id="answer"></p>
                    </div>
                    <div style="font-size: 24px; padding: 10px">
                        <label
                            style="color: black; font-weight: 500; float: left"
                            >Not Ver:</label
                        >
                        <input type="text" maxlength="3" id="grade" />
                        <p style="display: inline">/100</p>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button class="button is-success" @click="postGrade">
                        Kaydet
                    </button>
                    <button class="button" @click="toggleModal('.grade-modal')">
                        Çık
                    </button>
                </footer>
            </div>
        </div>
        <div class="modal delete-modal">
            <div
                class="modal-background"
                @click="toggleModal('.delete-modal')"
            ></div>
            <div class="modal-card">
                <section class="modal-card-body">
                    <div class="answer-block">
                        <p class="answer-header">
                            Bu ödevi silmek istediğinize emin misiniz?
                        </p>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button
                        class="button is-success"
                        @click="postDeleteHomework(chosenHomeworkDeleteId)"
                    >
                        Evet
                    </button>
                    <button
                        class="button"
                        @click="toggleModal('.delete-modal')"
                    >
                        Çık
                    </button>
                </footer>
            </div>
        </div>
        <div class="error-modal modal">
            <div
                class="modal-background"
                @click="toggleModal('.error-modal')"
            ></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Uyarı</p>
                    <button
                        class="delete"
                        @click="toggleModal('.error-modal')"
                        aria-label="close"
                    ></button>
                </header>
                <section class="modal-card-body">
                    Teslim tarihini en erken şuandan sonrasına
                    ayarlayabilirsiniz.
                </section>
            </div>
        </div>
        <div class="modal screenshot-modal">
            <div
                class="modal-background"
                @click="toggleModal('.screenshot-modal')"
            ></div>
            <div>
                <header class="modal-card-head">
                    <p class="modal-card-title"></p>
                    <button
                        class="delete"
                        @click="toggleModal('.screenshot-modal')"
                        aria-label="close"
                    ></button>
                </header>
                <div style="position: sticky">
                    <img
                        style="height: 65vh"
                        :src="screenshot_image_source"
                        alt=""
                    />
                </div>
            </div>
        </div>
        <div class="modal modal-audio">
            <div
                class="modal-background"
                @click="toggleModal('.modal-audio')"
            ></div>
            <div style="width: 40vw">
                <vue-plyr :key="audio_source">
                    <audio>
                        <source :src="audio_source" />
                    </audio>
                </vue-plyr>
            </div>
        </div>
    </div>
</template>

<script>
import TeacherService from "../../services/teacher.service";
import { mapGetters } from "vuex";
import { Datetime } from "vue-datetime";
import { Settings } from "luxon";
import moment from "moment";
moment.locale("tr");
Settings.defaultLocale = "tr";
export default {
    name: "StudentReports",
    data() {
        return {
            columns: [],
            columns_homeworks: [
                {
                    label: "Adı",
                    field: "homework_name",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Öğretmen",
                    field: "name",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Konu",
                    field: "parentlecture",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Kazanım",
                    field: "lecture_name",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Seviye",
                    field: "course_level",
                    type: "number",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Gönderim tarihi",
                    field: "created_at",
                    type: "date",
                    dateInputFormat: "yyyy-MM-dd HH:mm:SS",
                    dateOutputFormat: "dd.MM.yyyy HH:mm",
                },
                {
                    label: "Teslim tarihi",
                    field: "deadline",
                    type: "date",
                    dateInputFormat: "yyyy-MM-dd HH:mm:SS",
                    dateOutputFormat: "dd.MM.yyyy HH:mm",
                },
                {
                    label: "Teslim oranı",
                    field: "completed",
                    type: "percentage",
                },
                {
                    label: "Başarı oranı",
                    field: "avgpoint",
                    type: "percentage",
                },
                {
                    label: "Sil",
                    field: "delete",
                },
            ],
            columns_tab2_homework: [
                {
                    label: "Adı",
                    field: "name",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Öğretmen",
                    field: "teacher",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Konu",
                    field: "lecture",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Kazanım",
                    field: "sub_lecture",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Gönderim tarihi",
                    field: "createdAt",
                    type: "date",
                    dateInputFormat: "yyyy-MM-dd HH:mm:SS",
                    dateOutputFormat: "dd.MM.yyyy HH:mm",
                },
                {
                    label: "Teslim tarihi",
                    field: "deadline",
                    type: "date",
                    dateInputFormat: "yyyy-MM-dd HH:mm:SS",
                    dateOutputFormat: "dd.MM.yyyy HH:mm",
                },
                {
                    label: "Teslim oranı",
                    field: "submit_score",
                    type: "percentage",
                },
                {
                    label: "Başarı oranı",
                    field: "score",
                    type: "percentage",
                },
            ],
            columns_classes: [
                {
                    label: "Sınıf",
                    field: "class",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Adı",
                    field: "name",
                },
                {
                    label: "Öğretmen",
                    field: "teacher",
                },
                {
                    label: "Konu",
                    field: "lecture",
                },
                {
                    label: "Kazanım",
                    field: "sub_lecture",
                },
                {
                    label: "Teslim oranı",
                    field: "submit_score",
                    type: "percentage",
                },
                {
                    label: "Başarı oranı",
                    field: "score",
                    type: "percentage",
                },
            ],
            columns_students: [
                {
                    label: "Sınıf",
                    field: "class",
                },
                {
                    label: "Adı",
                    field: "name",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "No",
                    field: "code",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Teslim Tarihi",
                    field: "isSubmit",
                },
                {
                    label: "Başarım",
                    field: "score",
                },
                {
                    label: "süre",
                    field: "time",
                },
                {
                    label: "Yorum yap",
                    field: "comment",
                },
            ],
            columns_questions: [
                {
                    label: "Adı",
                    field: "name",
                },
                {
                    label: "Soru",
                    field: "question_index",
                },
                {
                    label: "Tipi",
                    field: "type",
                },
                {
                    label: "Başarım",
                    field: "score",
                },
                {
                    label: "Süre",
                    field: "time",
                },
                {
                    label: "Görsel",
                    field: "answer_image",
                },
                {
                    label: "Ses Kaydı",
                    field: "answer_audio",
                },
                {
                    label: "Not ver",
                    field: "grade",
                },
            ],
            columns_tab2_classes: [
                {
                    label: "Sınıf",
                    field: "class",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Teslim oranı",
                    field: "submit_score",
                    type: "percentage",
                },
                {
                    label: "Başarı oranı",
                    field: "score",
                    type: "percentage",
                },
            ],
            columns_courses: [
                {
                    label: "Seviye",
                    field: "level",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Ders",
                    field: "course",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Ödev Sayısı",
                    field: "homework_count",
                },
            ],
            columns_teachers: [
                {
                    label: "Öğretmen",
                    field: "teacher",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Ödev Sayısı",
                    field: "homework_count",
                },
            ],
            columns_manager_homeworks: [
                {
                    label: "Adı",
                    field: "name",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Teslim oranı",
                    field: "submit_score",
                    type: "percentage",
                },
                {
                    label: "Başarı oranı",
                    field: "score",
                    type: "percentage",
                },
            ],
            columns_manager_classes: [
                {
                    label: "Sınıf",
                    field: "class",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Ödev Sayısı",
                    field: "homework_count",
                },
            ],
            columns_manager_courses: [
                {
                    label: "Ders",
                    field: "course",
                    filterOptions: {
                        enabled: true, // enable filter for this column
                        placeholder: "Ara", // placeholder for filter input
                    },
                },
                {
                    label: "Ödev Sayısı",
                    field: "homework_count",
                },
            ],
            serverParams: {
                columnFilters: {},
                sort: {
                    field: "",
                    type: "",
                },
                page: 1,
            },

            rows: [],
            homeworks: [],
            stage: 0,
            chosen_homework: {},
            chosen_class: {},
            chosen_student: {},
            chosen_course: {},
            chosen_teacher: {},
            chosen_class_manager: {},
            chosen_homework_manager: {},
            enableClick: 1,
            chosenHomeworkUser: 0,
            answers: [],
            chosenAnswerId: 0,
            chosenHomeworkDeleteId: 0,
            chosenHomeworkEditId: 0,
            pickDateTime: 0,
            deadline: "",
            deadlineIndex: 0,
            minDatetime: "",
            isSort: 1,
            screenshot_image_source: "",
            audio_source: "",
            audio_player_key: 0,
            loading: false,
            totalRecords: 100,
        };
    },
    components: {
        datetime: Datetime,
    },
    methods: {
        async formatTable(info) {
            if (this.enableClick) {
                this.serverParams = {
                    columnFilters: {},
                    sort: {
                        field: "",
                        type: "",
                    },
                    page: 1,
                };
                this.loading = true;
                if (this.getUser === 1) {
                    if (this.getTab === 0) {
                        if (this.stage === 0) {
                            await this.getAllHomeworks();
                            this.loading = false;
                            this.stage = 1;
                        } else if (this.stage === 1) {
                            if (info) {
                                this.chosen_homework = info.row;
                                await this.getHomeworkClasses(info.row.id);
                                this.loading = false;
                            } else {
                                await this.getHomeworkClasses(
                                    this.chosen_homework.id
                                );
                                this.loading = false;
                            }
                        } else if (this.stage === 2) {
                            if (info) {
                                this.chosen_class = info.row;
                                await this.getHomeworkClassStudents(
                                    this.chosen_homework.id,
                                    info.row.id
                                );
                                this.loading = false;
                            } else {
                                await this.getHomeworkClassStudents(
                                    this.chosen_homework.id,
                                    this.chosen_class.id
                                );
                                this.loading = false;
                            }
                            this.stage = 3;
                        } else if (this.stage === 3) {
                            if (info) {
                                this.chosen_student = info.row;
                                await this.getStudentAnswers(
                                    this.chosen_homework.id,
                                    info.row.id
                                );
                                this.loading = false;
                            } else {
                                await this.getStudentAnswers(
                                    this.chosen_homework.id,
                                    this.chosen_student.id
                                );
                                this.loading = false;
                            }
                            this.stage = 4;
                        } else if (this.stage === 4) {
                            this.loading = false;
                        }
                    }
                    if (this.getTab === 1) {
                        if (this.stage === 0) {
                            await this.getTab2Classes();
                            this.loading = false;
                            this.stage = 1;
                        } else if (this.stage === 1) {
                            if (info) {
                                this.chosen_class = info.row;
                                await this.getTab2Homeworks(info.row.id);
                                this.loading = false;
                            } else {
                                await this.getTab2Homeworks(
                                    this.chosen_class.id
                                );
                                this.loading = false;
                            }
                            this.stage = 2;
                        } else if (this.stage === 2) {
                            if (info) {
                                this.chosen_homework = info.row;
                                await this.getHomeworkClassStudents(
                                    info.row.id,
                                    this.chosen_class.id
                                );
                                this.loading = false;
                            } else {
                                await this.getHomeworkClassStudents(
                                    this.chosen_homework.id,
                                    this.chosen_class.id
                                );
                                this.loading = false;
                            }
                            this.stage = 3;
                        } else if (this.stage === 3) {
                            if (info) {
                                this.chosen_student = info.row;
                                await this.getStudentAnswers(
                                    this.chosen_homework.id,
                                    info.row.id
                                );
                                this.loading = false;
                            } else {
                                await this.getStudentAnswers(
                                    this.chosen_homework.id,
                                    this.chosen_student.id
                                );
                                this.loading = false;
                            }
                            this.stage = 4;
                        } else if (this.stage === 4) {
                            this.loading = false;
                        }
                    }
                }
                if (this.getUser === 2) {
                    if (this.getTab === 0) {
                        if (this.stage === 0) {
                            await this.getCourses();
                            this.loading = false;
                            this.stage = 1;
                        } else if (this.stage === 1) {
                            if (info) {
                                this.chosen_course = info.row;
                                await this.getTeachers(info.row.id);
                                this.loading = false;
                            } else {
                                await this.getTeachers(this.chosen_course.id);
                                this.loading = false;
                            }
                            this.stage = 2;
                        } else if (this.stage === 2) {
                            if (info) {
                                this.chosen_teacher = info.row;
                                await this.getHomeworkCoursesTeacher(
                                    this.chosen_course.id,
                                    info.row.id
                                );
                                this.loading = false;
                            } else {
                                await this.getHomeworkCoursesTeacher(
                                    this.chosen_course.id,
                                    this.chosen_teacher.id
                                );
                                this.loading = false;
                            }
                            this.stage = 3;
                        } else if (this.stage === 3) {
                            this.loading = false;
                        }
                    }
                    if (this.getTab === 1) {
                        if (this.stage === 0) {
                            await this.getManagerClasses();
                            this.loading = false;
                            this.stage = 1;
                        } else if (this.stage === 1) {
                            if (info) {
                                this.chosen_class_manager = info.row;
                                await this.getManagerCourses(info.row.id);
                                this.loading = false;
                            } else {
                                await this.getManagerCourses(
                                    this.chosen_class_manager.id
                                );
                                this.loading = false;
                            }
                            this.stage = 2;
                        } else if (this.stage === 2) {
                            if (info) {
                                this.chosen_homework_manager = info.row;
                                await this.getManagerHomeworks(
                                    this.chosen_class_manager.id,
                                    info.row.id
                                );
                                this.loading = false;
                            } else {
                                await this.getManagerHomeworks(
                                    this.chosen_class_manager.id,
                                    this.chosen_homework_manager.id
                                );
                                this.loading = false;
                            }
                            this.stage = 3;
                        } else if (this.stage === 3) {
                            this.loading = false;
                        }
                    }
                }
            }
        },
        goBack() {
            if (this.stage > 0) {
                this.stage = this.stage === 1 ? this.stage - 1 : this.stage - 2;
                this.formatTable();
            }
        },
        async getAllHomeworks(params) {
            await TeacherService.getAllHomeworks(params).then((res) => {
                if (res) {
                    this.columns = this.columns_homeworks;
                    this.homeworks = res.data.data;
                    this.rows = [];
                    this.homeworks.forEach((element) => {
                        this.rows.push({
                            id: element.id,
                            homework_name: element.homework_name,
                            name: element.name,
                            parentlecture: element.parentlecture
                                ? element.parentlecture
                                : element.lecture_name,
                            lecture_name: element.parentlecture
                                ? element.lecture_name
                                : "",
                            course_level: element.course_level,
                            created_at: moment(element.created_at).format(
                                "YYYY-MM-DD HH:mm:ss"
                            ),
                            deadline: element.deadline
                                ? element.deadline
                                : "1970-01-01 00:00:00",
                            avgpoint: element.avgpoint / 100,
                            completed: element.completed / 100,
                            delete: "",
                        });
                    });
                    this.totalRecords = res.data.total;
                }
            });
        },
        async getHomeworkClasses(homework_id) {
            this.stage = 2;
            await TeacherService.getHomeworkClasses(homework_id).then((res) => {
                if (res) {
                    this.columns = this.columns_classes;
                    const classes = res.data;
                    this.rows = [];

                    classes.forEach((element) => {
                        this.rows.push({
                            id: element.id,
                            class: element.class_name,
                            name: this.chosen_homework.homework_name,
                            teacher: this.chosen_homework.name,
                            lecture: this.chosen_homework.parentlecture,
                            sub_lecture: this.chosen_homework.lecture_name,
                            score: element.point_avg / 100,
                            submit_score: element.done_percent / 100,
                        });
                    });
                }
            });
        },
        async getHomeworkClassStudents(homework_id, class_id) {
            await TeacherService.getHomeworkClassStudents(
                homework_id,
                class_id
            ).then((res) => {
                if (res) {
                    this.columns = this.columns_students;
                    const students = res.data;
                    this.rows = [];
                    students.forEach((element) => {
                        this.rows.push({
                            id: element.id,
                            class: this.chosen_class.class,
                            name: element.name,
                            code: element.code,
                            isSubmit: element.homework_user.completed_date
                                ? element.homework_user.completed_date
                                : "",
                            score: element.homework_user.completed_date
                                ? element.homework_user.point + "%"
                                : "",
                            time: element.homework_user.completed_date
                                ? ~~(element.homework_user.total_time / 60) +
                                  ":" +
                                  (
                                      "0" +
                                      ~~(element.homework_user.total_time % 60)
                                  ).slice(-2)
                                : "",
                            comment: "",
                            isSubmitBool: element.homework_user.completed_date
                                ? 1
                                : 0,
                        });
                    });
                }
            });
        },
        async getStudentAnswers(homework_id, class_id) {
            await TeacherService.getStudentAnswers(homework_id, class_id).then(
                (res) => {
                    if (res) {
                        this.columns = this.columns_questions;
                        this.answers = res.data ? res.data : [];
                        this.rows = [];
                        let questionIndex = this.answers[0]
                            ? this.answers[0].question_index - 1
                            : 0;
                        this.answers.forEach((element) => {
                            if (questionIndex === element.question_index) {
                                return;
                            }
                            element.type = String(element.type);
                            this.rows.push({
                                id: element.id,
                                question_index: element.question_index,
                                name: this.chosen_student.name,
                                type: element.type
                                    ? element.type === "0"
                                        ? "Çoktan Seçmeli"
                                        : element.type === "1"
                                        ? "Boşluk Doldurma"
                                        : element.type === "2"
                                        ? "Sürükle Bırak"
                                        : element.type === "3"
                                        ? "Eşleştirme"
                                        : element.type === "4"
                                        ? "Doğru Yanlış"
                                        : element.type === "5"
                                        ? "Açık Uçlu Soru"
                                        : ""
                                    : "",
                                score:
                                    element.type === "5"
                                        ? element.success_percent === null
                                            ? "Not Verilmedi"
                                            : element.success_percent + "%"
                                        : element.success_percent
                                        ? element.success_percent + "%"
                                        : "0%",
                                time:
                                    ~~(element.time / 60) +
                                    ":" +
                                    ("0" + ~~(element.time % 60)).slice(-2),
                                grade: "",
                                isSubmitBool:
                                    element.type === "5"
                                        ? element.success_percent === null
                                            ? 0
                                            : 1
                                        : 1,
                                isGraded:
                                    element.type === "5"
                                        ? element.success_percent === null
                                            ? 0
                                            : 1
                                        : 0,
                                answer_image: element.screenshot,
                                answer_audio: element.audio,
                            });
                            questionIndex = element.question_index;
                        });
                    }
                }
            );
            this.getImagesandSounds(homework_id, class_id);
        },
        async getTab2Classes() {
            await TeacherService.getTab2Classes().then((res) => {
                if (res) {
                    this.columns = this.columns_tab2_classes;
                    const classes = res.data;
                    this.rows = [];
                    classes.forEach((element) => {
                        this.rows.push({
                            id: element.id,
                            class: element.class_name,
                            score: element.avg / 100,
                            submit_score: element.done_percent / 100,
                        });
                    });
                }
            });
        },
        async getTab2Homeworks(class_id) {
            await TeacherService.getTab2Homeworks(class_id).then((res) => {
                if (res) {
                    this.columns = this.columns_tab2_homework;
                    const homeworks = res.data;
                    this.rows = [];
                    homeworks.forEach((element) => {
                        this.rows.push({
                            id: element.id,
                            name: element.homework_name,
                            teacher: element.teacher.name,
                            lecture: element.lecture
                                ? element.lecture.parentlecture
                                    ? element.lecture.parentlecture.lecture_name
                                    : element.lecture.lecture_name
                                : "",
                            sub_lecture: element.lecture
                                ? element.lecture.parentlecture
                                    ? element.lecture.lecture_name
                                    : ""
                                : "",
                            level: element.course.course_level,
                            createdAt:
                                element.created_at.split("T")[0] +
                                " " +
                                element.created_at.split("T")[1].split(".")[0],
                            deadline: element.deadline
                                ? element.deadline.deadline
                                : "1970-01-01 00:00:00",
                            score: element.avg / 100,
                            submit_score: element.done_percent / 100,
                        });
                    });
                }
            });
        },
        async getCourses() {
            await TeacherService.getCourses().then((res) => {
                if (res) {
                    this.columns = this.columns_courses;
                    const courses = res.data;
                    this.rows = [];
                    courses.forEach((element) => {
                        this.rows.push({
                            id: element.homework_course_id,
                            level: element.course_level,
                            course: element.course_name,
                            homework_count: element.total,
                        });
                    });
                }
            });
        },
        async getTeachers(course_id) {
            await TeacherService.getTeachers(course_id).then((res) => {
                if (res) {
                    this.columns = this.columns_teachers;
                    const teachers = res.data;
                    this.rows = [];
                    teachers.forEach((element) => {
                        this.rows.push({
                            id: element.homework_publisher_id,
                            teacher: element.name,
                            homework_count: element.total,
                        });
                    });
                }
            });
        },

        async getHomeworkCoursesTeacher(course_id, teacher_id) {
            await TeacherService.getHomeworkCoursesTeacher(
                course_id,
                teacher_id
            ).then((res) => {
                if (res) {
                    this.columns = this.columns_manager_homeworks;
                    const homeworks = res.data;
                    this.rows = [];
                    homeworks.forEach((element) => {
                        this.rows.push({
                            id: element.id,
                            name: element.homework_name,
                            submit_score: element.done_percent / 100,
                            score: element.point_avg / 100,
                        });
                    });
                }
            });
        },
        async getManagerClasses() {
            await TeacherService.getManagerClasses().then((res) => {
                if (res) {
                    this.columns = this.columns_manager_classes;
                    const classes = res.data;
                    this.rows = [];
                    classes.forEach((element) => {
                        this.rows.push({
                            id: element.class_id,
                            class: element.class_name,
                            homework_count: element.count,
                        });
                    });
                }
            });
        },
        async getManagerCourses(class_id) {
            await TeacherService.getManagerClasses(class_id).then((res) => {
                if (res) {
                    this.columns = this.columns_manager_courses;
                    const courses = res.data;
                    this.rows = [];
                    courses.forEach((element) => {
                        this.rows.push({
                            id: element.homework_course_id,
                            course: element.course_name,
                            homework_count: element.count,
                        });
                    });
                }
            });
        },
        async getManagerHomeworks(class_id, course_id) {
            await TeacherService.getManagerClasses(class_id, course_id).then(
                (res) => {
                    if (res) {
                        this.columns = this.columns_manager_homeworks;
                        const homeworks = res.data;
                        this.rows = [];
                        homeworks.forEach((element) => {
                            this.rows.push({
                                id: element.homework_id,
                                name: element.homework_name,
                                submit_score: element.completed / element.count,
                                score: Number(element.avg) / 100,
                            });
                        });
                    }
                }
            );
        },
        async comment(info) {
            //TODO
            const self = this;
            this.enableClick = 0;
            await TeacherService.getHomeworkClassStudents(
                this.chosen_homework.id,
                this.chosen_class.id
            ).then((res) => {
                document.getElementById("comment").value = res.data.find(
                    (element) => {
                        return element.id === info.id;
                    }
                ).homework_user.teacher_comment;
                self.chosenHomeworkUser = res.data.find((element) => {
                    return element.id === info.id;
                }).homework_user.id;
            });
            this.toggleModal(".comment-modal");
            setTimeout(function () {
                self.enableClick = 1;
            }, 2000);
        },
        async postComment() {
            TeacherService.postComment(
                this.chosenHomeworkUser,
                document.getElementById("comment").value
            ).then((res) => {
                console.log(res);
            });
            this.toggleModal(".comment-modal");
        },
        toggleModal(class_name) {
            if (
                document
                    .querySelector(class_name)
                    .classList.contains("is-active")
            ) {
                document
                    .querySelector(class_name)
                    .classList.remove("is-active");
            } else {
                document.querySelector(class_name).classList.add("is-active");
            }
        },
        grade(info) {
            document.getElementById("answer").innerText = this.answers.find(
                (element) => {
                    return element.id === info.id;
                }
            ).description;
            this.chosenAnswerId = info.id;

            this.toggleModal(".grade-modal");
        },
        showScreenshot(info) {
            this.screenshot_image_source = info.answer_image;
            this.toggleModal(".screenshot-modal");
        },
        showAudio(info) {
            this.audio_source = info.answer_audio;
            this.toggleModal(".modal-audio");
        },
        async postGrade() {
            await TeacherService.rateQuestion(
                this.chosenAnswerId,
                document.getElementById("grade").value
            ).then((res) => {
                document.getElementById("grade").value = "";
            });
            this.refresh();
            this.toggleModal(".grade-modal");
        },
        refresh() {
            this.rows = [];
            this.stage -= 1;
            const self = this;
            setTimeout(function () {
                self.formatTable();
            }, 100);
        },
        deleteHomework(info) {
            this.enableClick = 0;
            this.chosenHomeworkDeleteId = info.id;
            this.toggleModal(".delete-modal");
        },
        postDeleteHomework(id) {
            TeacherService.deleteHomework(id);
            this.enableClick = 1;
            this.refresh();
            this.toggleModal(".delete-modal");
        },
        edit(info) {
            const min_date = new Date();
            this.enableClick = 0;
            this.pickDateTime = 1;
            this.deadline = info.deadline.split(" ").join("T");
            this.minDatetime = moment(min_date)
                .format("YYYY-MM-DD HH:mm:ss")
                .split(" ")
                .join("T");
            this.deadlineIndex = info.originalIndex;
            this.chosenHomeworkEditId = info.id;
        },
        async confirmEdit() {
            const now = new Date();
            if (this.compareDates(this.deadline, now)) {
                await TeacherService.changeDeadline(
                    this.chosenHomeworkEditId,
                    this.chosenHomeworkEditId,
                    moment(this.deadline).format("YYYY-MM-DD HH:mm:ss")
                );
                this.enableClick = 1;
                this.refresh();
                this.pickDateTime = 0;
            } else {
                this.toggleModal(".error-modal");
            }
        },
        cancel() {
            this.pickDateTime = 0;
            const self = this;
            setTimeout(function () {
                self.enableClick = 1;
            }, 100);
        },
        compareDates(date1, date2) {
            if (!(date1 == null || date2 == null)) {
                const dateOne = new Date(date1);
                const dateTwo = new Date(date2);
                return dateOne > dateTwo;
            } else {
                return false;
            }
        },
        getImagesandSounds(homework_id, student_id) {
            TeacherService.getStudentAnswers(homework_id, student_id, 1).then(
                (res) => {
                    this.rows = [
                        ...this.rows.map((row) => {
                            row.answer_image = res.data.filter((answer) => {
                                return (
                                    answer.question_index === row.question_index
                                );
                            })[0].screenshot;
                            row.answer_audio = res.data.filter((answer) => {
                                return (
                                    answer.question_index === row.question_index
                                );
                            })[0].audio;
                            return row;
                        }),
                    ];
                }
            );
        },
        updateParams(newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },

        onPageChange(params) {
            this.updateParams({ page: params.currentPage });
            this.loadItems();
        },
        onSortChange(params) {
            if (!(params[0].field === "delete")) {
                this.updateParams({
                    sort: [
                        {
                            type:
                                params[0].type[0].toUpperCase() +
                                params[0].type.substring(1),
                            field: params[0].field,
                        },
                    ],
                });
            }
            this.loadItems();
        },

        onColumnFilter(params) {
            this.updateParams(params);
            this.loadItems();
        },
        loadItems() {
            const page_params = this.serverParams.page
                ? `page=${this.serverParams.page}`
                : "";

            const filter_params = Object.entries(
                this.serverParams.columnFilters
            )
                .map(([k, v]) => `${k}=${v}`)
                .join("&");

            const sort_params = this.serverParams.sort[0]
                ? `sort${this.serverParams.sort[0].type}=${this.serverParams.sort[0].field}`
                : "";
            this.reorderTable(`${sort_params}&${filter_params}&${page_params}`);
        },

        async reorderTable(params) {
            if (this.enableClick) {
                this.loading = true;
                if (this.getUser === 1) {
                    if (this.getTab === 0) {
                        if (this.stage === 1) {
                            await this.getAllHomeworks(params);
                            this.loading = false;
                        }
                    }
                }
            }
        },
    },
    mounted() {
        this.formatTable();
    },
    computed: {
        ...mapGetters({
            getTab: "getTab",
            getUser: "getUser",
        }),
    },
};
</script>
<style scoped>
.main-table {
    padding: 40px 40px 65px 40px;
    text-align: center;
}
#comment {
    height: 400px;
    width: 100%;
    resize: none;
    padding: 10px;
}
.back {
    margin: 40px 40px;
}
#grade {
    width: 48px;
    text-align: center;
}
#answer {
    min-height: 150px;
    padding: 10px;
    border: black 1px solid;
}
.answer-header {
    color: black;
    font-weight: 600;
    padding: 10px 0;
}
.answer-block {
    text-align: left;
    padding: 10px;
}
.grade-modal {
    text-align: center;
}
.grade-button,
.comment-button,
.delete-button {
    width: 100%;
    height: 25px;
}
.button {
    transition: opacity 150ms linear, background-color 150ms linear;
}
.button:hover,
.button:focus {
    opacity: 0.8;
}
.bg {
    height: 25px;
    border: 1px solid green;
    width: 100%;
    margin: 0 auto;
}
.html {
    background: mediumseagreen;
    width: 80%;
    height: 23px;
    line-height: 30px;
    color: #000;
    background-size: 50px 50px;
    max-width: 100%;
}
.percentage {
    color: black;
    float: right;
    margin-top: -24px;
}
.edit-button {
    background: url("/images/liconlar/edit_icon.png") no-repeat;
    padding: 0;
    background-size: 21px;
    width: 23px;
    height: 23px;
}
.cancel-button {
    background: url("/images/liconlar/cancel_icon.png") no-repeat;
    padding: 0;
    background-size: 28px;
    width: 30px;
    height: 30px;
    margin: 4px;
}
.confirm-button {
    background: url("/images/liconlar/confirm_icon.png") no-repeat;
    padding: 0;
    margin: 4px;
    background-size: 28px;
    width: 30px;
    height: 30px;
}
.image-button {
    background: url("/images/liconlar/image_icon.svg") no-repeat;
    background-size: 31px;
    width: 30px;
    height: 33px;
}
.sound-button {
    background: url("/images/liconlar/sound_image.png") no-repeat;
    background-size: 31px;
    width: 32px;
    height: 33px;
}
.padding-top-120 {
    padding-top: 159px;
}
</style>
<style>
.vdatetime-input {
    width: 131px !important;
}
</style>
