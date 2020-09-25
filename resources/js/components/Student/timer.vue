<template>
    <div class="timer">
        <div class="timer--clock">
            <div class="minutes-group clock-display-grp">
                <div class="first number-grp" style="padding-left: 1px;">
                    <div class="number-grp-wrp">
                        <div class="num num-0"><p>0</p></div>
                        <div class="num num-1"><p>1</p></div>
                        <div class="num num-2"><p>2</p></div>
                        <div class="num num-3"><p>3</p></div>
                        <div class="num num-4"><p>4</p></div>
                        <div class="num num-5"><p>5</p></div>
                        <div class="num num-6"><p>6</p></div>
                        <div class="num num-7"><p>7</p></div>
                        <div class="num num-8"><p>8</p></div>
                        <div class="num num-9"><p>9</p></div>
                    </div>
                </div>
                <div class="second number-grp">
                    <div class="number-grp-wrp">
                        <div class="num num-0"><p>0</p></div>
                        <div class="num num-1"><p>1</p></div>
                        <div class="num num-2"><p>2</p></div>
                        <div class="num num-3"><p>3</p></div>
                        <div class="num num-4"><p>4</p></div>
                        <div class="num num-5"><p>5</p></div>
                        <div class="num num-6"><p>6</p></div>
                        <div class="num num-7"><p>7</p></div>
                        <div class="num num-8"><p>8</p></div>
                        <div class="num num-9"><p>9</p></div>
                    </div>
                </div>
            </div>
            <div class="clock-separator"><p>:</p></div>
            <div class="seconds-group clock-display-grp">
                <div class="first number-grp">
                    <div class="number-grp-wrp">
                        <div class="num num-0"><p>0</p></div>
                        <div class="num num-1"><p>1</p></div>
                        <div class="num num-2"><p>2</p></div>
                        <div class="num num-3"><p>3</p></div>
                        <div class="num num-4"><p>4</p></div>
                        <div class="num num-5"><p>5</p></div>
                        <div class="num num-6"><p>6</p></div>
                        <div class="num num-7"><p>7</p></div>
                        <div class="num num-8"><p>8</p></div>
                        <div class="num num-9"><p>9</p></div>
                    </div>
                </div>
                <div class="second number-grp">
                    <div class="number-grp-wrp">
                        <div class="num num-0"><p>0</p></div>
                        <div class="num num-1"><p>1</p></div>
                        <div class="num num-2"><p>2</p></div>
                        <div class="num num-3"><p>3</p></div>
                        <div class="num num-4"><p>4</p></div>
                        <div class="num num-5"><p>5</p></div>
                        <div class="num num-6"><p>6</p></div>
                        <div class="num num-7"><p>7</p></div>
                        <div class="num num-8"><p>8</p></div>
                        <div class="num num-9"><p>9</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { TweenMax } from "gsap";

export default {
    name: "timer",
    components: {},
    methods: {
        startCountdown(time) {
            let self = this;
            initTimer(time); // other ways --> "0:15" "03:5" "5:2"
            var timerEl = document.querySelector(".timer");

            function initTimer(t) {
                var self = this,
                    timerEl = document.querySelector(".timer"),
                    minutesGroupEl = timerEl.querySelector(".minutes-group"),
                    secondsGroupEl = timerEl.querySelector(".seconds-group"),
                    minutesGroup = {
                        firstNum: minutesGroupEl.querySelector(".first"),
                        secondNum: minutesGroupEl.querySelector(".second"),
                    },
                    secondsGroup = {
                        firstNum: secondsGroupEl.querySelector(".first"),
                        secondNum: secondsGroupEl.querySelector(".second"),
                    };

                var time = {
                    min: t.split(":")[0],
                    sec: t.split(":")[1],
                };

                var timeNumbers;

                function updateTimer() {
                    var timestr;
                    var date = new Date();

                    date.setHours(0);
                    date.setMinutes(time.min);
                    date.setSeconds(time.sec);

                    var newDate = new Date(date.valueOf() - 1000);
                    var temp = newDate.toTimeString().split(" ");
                    var temp_split = temp[0].split(":");

                    time.min = temp_split[1];
                    time.sec = temp_split[2];

                    timestr = time.min + time.sec;
                    timeNumbers = timestr.split("");
                    updateTimerDisplay(timeNumbers);

                    if (timestr === "0000") countdownFinished();

                    if (timestr !== "0000") setTimeout(updateTimer, 1000);
                }

                function updateTimerDisplay(arr) {
                    animateNum(minutesGroup.firstNum, arr[0]);
                    animateNum(minutesGroup.secondNum, arr[1]);
                    animateNum(secondsGroup.firstNum, arr[2]);
                    animateNum(secondsGroup.secondNum, arr[3]);
                }

                function animateNum(group, arrayValue) {
                    TweenMax.killTweensOf(
                        group.querySelector(".number-grp-wrp")
                    );
                    TweenMax.set(group.querySelector(".number-grp-wrp"), {
                        y: -group.querySelector(".num-" + arrayValue).offsetTop,
                    });
                }

                setTimeout(updateTimer, 1000);
            }

            function countdownFinished() {
                self.$emit("toggle-modal", ".time-is-up-modal");
                self.$emit("submitHomework", ".time-is-up-modal");
                setTimeout(self.$emit("go-back"), 1500);
            }
        },
    },
    props: ["jsonQuestions", "isLoaded", "preQuestion"],

    watch: {
        isLoaded: function () {
            if (
                this.jsonQuestions.data.homework_time &&
                !this.$store.state.homework.isDone
            ) {
                var minutes = Math.floor(
                    this.jsonQuestions.data.homework_time / 60
                );
                var seconds =
                    this.jsonQuestions.data.homework_time - minutes * 60;

                let time = minutes + ":" + seconds;
                this.startCountdown(time);
            }
        },
    },
};
</script>
<style scoped>
* {
    margin: 0;
    padding: 0;
    font-family: "Arimo", sans-serif;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

body {
    background: #d7d7d7;
}
.timer {
    width: 92px;
    right: 0;
    padding: 3px 4px 3px 4px;
    margin-right: -1px;
    position: absolute;
    display: inline-block;
    background-color: aliceblue;
    color: #5679b3;
    border: 1px solid gray;
}
.timer * {
    cursor: default;
}
.timer h3 {
    width: 100%;
    font-size: 26px;
    letter-spacing: 4px;
    text-align: center;
}
.timer--clock {
    width: 100%;
    position: relative;

    overflow: hidden;
}
.timer--clock .clock-display-grp {
    width: 100%;
    position: relative;
}
.timer--clock .clock-display-grp .number-grp {
    width: auto;
    display: block;
    height: 30px;
    float: left;
    overflow: hidden;
}
.timer--clock .clock-display-grp .number-grp .number-grp-wrp {
    width: 100%;
    position: relative;
}
.timer--clock .clock-display-grp .number-grp .number-grp-wrp .num {
    width: 100%;
    position: relative;
    height: 30px;
}
.timer--clock .clock-display-grp .number-grp .number-grp-wrp .num p {
    width: auto;
    display: table;
    font-size: 30px;
    line-height: 30px;
}

.timer--clock .clock-separator {
    width: auto;
    float: left;
    display: block;
    height: 30px;
}
.timer--clock .clock-separator p {
    width: auto;
    display: table;
    font-size: 30px;
    line-height: 27px;
}
.timer h4 {
    width: 100%;
    font-size: 10px;
    letter-spacing: 6px;
    text-align: center;
    padding-top: 25px;
    float: left;
}
</style>
