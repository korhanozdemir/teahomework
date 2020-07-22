<template>
  <div class="home">
      <section class="section">
          <div v-if="isLoaded" class="container">
              <h1 class="title" >
                  {{jsonQuestions.data.homework_name}}
              </h1>
              <p  class="subtitle">
                  Soru <strong>{{questionIndex+1}}</strong>
              </p>
              <p v-if="jsonQuestions.data.questions[questionIndex].question_type === 0" class="subtitle">
                  Doğru şıkkı <strong>SEÇ</strong>!
              </p>
              <p v-if="jsonQuestions.data.questions[questionIndex].question_type === 1" class="subtitle">
                  Boşlukları <strong>DOLDUR</strong>!
              </p>
              <p v-if="jsonQuestions.data.questions[questionIndex].question_type === 3" class="subtitle">
                  Seçenekleri doğru şekilde <strong>EŞLEŞTİR</strong>!
              </p>
              <p v-if="jsonQuestions.data.questions[questionIndex].question_type === 2" class="subtitle">
                  Doğru seçeneği  <strong>SÜRÜKLE</strong> ve <strong>BIRAK</strong> !
              </p>
              <p v-if="jsonQuestions.data.questions[questionIndex].question_type === 4" class="subtitle">
                  Doğru kutucukları <strong>İŞARETLE</strong>!
              </p>
              <p v-if="jsonQuestions.data.questions[questionIndex].question_type === 5" class="subtitle">
                  Soruyu çöz ve <strong>YORUMLA</strong>!
              </p>
          </div>
      </section>

          <main-area v-on:prev-question="save('prev')" v-on:next-question="save('next')" :isLoaded="isLoaded" :jsonQuestions.sync="jsonQuestions" :preQuestion.sync="preQuestion" :questionIndex="questionIndex"  />
  </div>
</template>

<script>

    import mainArea from "./main-area.vue"
    import Draggable from "gsap/Draggable"
    import { TweenLite } from 'gsap'
    import gsap from 'gsap'
    import 'bulma/css/bulma.css'
    import StudentService from "../../services/student.service";
    gsap.registerPlugin(Draggable)

export default {
  name: 'Home',
    data(){
        return {
            isLoaded: false,
            jsonQuestions: null,
            questionIndex: 0,
            student_answers: [],
            preQuestion: String

        }},
  components: {
      mainArea
  },
    methods: {
        nextQuestion (){
            if (this.questionIndex < this.jsonQuestions.data.questions.length-1 ){
                this.questionIndex += 1;
                setTimeout(this.dragger, 1000)
                setTimeout(this.matcher, 1100)
                if(document.querySelector('.question-area-canvas canvas')){
                    const elem = document.querySelectorAll('.question-area-canvas canvas');
                    for(let i=0;i<elem.length;i++){
                        document.querySelector('.question-area-canvas').removeChild(elem[i])
                    }
                }if (!document.querySelector(".question-area-canvas").classList.contains("resetted")){

                    document.querySelector('.question-area-canvas').classList.add("resetted")
                }
           }
        },save(state){
            this.preQuestion = state;
        },
        prevQuestion (){
            if( this.questionIndex > 0){
                this.questionIndex -= 1;
                console.log(this.questionIndex)
                setTimeout(this.dragger, 1000)
                setTimeout(this.matcher, 1100)
                if(document.querySelector('.question-area-canvas canvas')){
                    const elem = document.querySelectorAll('.question-area-canvas canvas');
                    for(let i=0;i<elem.length;i++){
                        document.querySelector('.question-area-canvas').removeChild(elem[i])
                    }
                }if (!document.querySelector(".question-area-canvas").classList.contains("resetted")){

                    document.querySelector('.question-area-canvas').classList.add("resetted")
                }
            }},
        dragger (){
            if(document.getElementsByClassName("answer_image")[0]){

                    const overlapThreshold = '10%'
                    let foundIndex = null //current droparea index user is hovering
                    Draggable.create(document.querySelectorAll(".dragarea"), {
                        type: 'x,y',
                        throwProps: true,
                        bounds: '.canvas',
                        onDrag(e) {
                            let i = document.querySelectorAll(".droparea").length
                            while (--i > -1) {
                                if (this.hitTest(document.querySelectorAll(".droparea")[i], overlapThreshold)) {
                                    debugger
                                    foundIndex = i
                                    TweenLite.set(document.querySelectorAll(".droparea")[foundIndex], {
                                        border: '#f99c0b 3px solid',
                                    })
                                    return
                                } else {
                                    foundIndex = null
                                    if(document.querySelectorAll(".empty_rect.droparea").length>0){
                                        debugger
                                        TweenLite.set(document.querySelectorAll(".droparea"),  {
                                            opacity: 1,
                                            border: '2px black solid'
                                        })
                                    }
                                    else{
                                    TweenLite.to(document.querySelectorAll(".droparea")[i], 0.5, {
                                        opacity: 1,
                                        border: 'none'
                                    })}
                                }
                            }
                        },
                        onDragEnd() {
                            console.log(foundIndex)
                            if (foundIndex === null) {
                                // move to original pos
                                TweenLite.to(this.target, 0.2, {
                                    x: 0,
                                    y: 0,
                                    width:281+"px",
                                    height:47+"px",
                                    backgroundSize:  281 + "px"

                                })
                                TweenLite.to(this.target.children[0], 0.2, {
                                    top: "50%"
                                })
                                return
                            }
                            //element hit-test passed so i want to postion it in the droparea and scale dragarea to cover droparea
                            const dropBound = document.querySelectorAll(".droparea")[foundIndex].getBoundingClientRect()
                            const dragBound = this.target.getBoundingClientRect()
                            if(document.querySelectorAll(".empty_rect.droparea").length>0){

                                TweenLite.to(this.target, 0.5, {
                                    opacity: 0
                                })
                                TweenLite.set(this.target,  {
                                    x: 0,
                                    y: 0,
                                })
                                TweenLite.to(this.target, 0.5, {
                                    opacity: 1
                                })
                                document.querySelectorAll(".droparea")[foundIndex].firstChild.firstChild.innerText = this.target.firstChild.firstChild.innerText
                                debugger
                                TweenLite.set(document.querySelectorAll(".droparea"),  {
                                    opacity: 1,
                                    border: '2px black solid'
                                })
                            }
                            else{
                            TweenLite.to(this.target, 0.2, {
                                x: "+=" + (dropBound.x - dragBound.x),
                                y: "+=" + (dropBound.y - dragBound.y+4) ,
                                backgroundSize:  dropBound.width + "px",
                                width:  dropBound.width


                        })
                            TweenLite.to(this.target.children[0], 0.2, {
                                top: "38%"
                            })
                                TweenLite.set(document.querySelectorAll(".droparea"), {
                                    opacity: 1,
                                    border: 'none'
                                })}
                        }
                    })
                }


              },
        matcher(){
            if(document.getElementsByClassName("match")[0]){
                var clicked = [];
                var hoverElement;
                var lineElem
                function drawLineXY(fromXY, toXY) {

                    if(!document.querySelector(".question-area-canvas canvas")) {
                        lineElem = document.createElement('canvas');
                        lineElem.style.position = "absolute";
                        document.querySelector(".question-area-canvas").appendChild(lineElem);
                    }
                    lineElem.width = 570;
                    lineElem.height = 530;
                    lineElem.style.left =0;
                    lineElem.style.top = 0;
                    var ctx = lineElem.getContext('2d');
                    // Use the identity matrix while clearing the canvas

                    ctx.save();
                    ctx.setTransform(1, 0, 0, 1, 0, 0);
                    ctx.clearRect(0, 0, lineElem.width, lineElem.height);
                    ctx.restore();
                    ctx.lineWidth = 4;
                    ctx.strokeStyle = 'black';
                    ctx.beginPath();
                    ctx.moveTo(fromXY.x+10, fromXY.y+10 );
                    ctx.lineTo(toXY.x+10 , toXY.y+10 );
                    ctx.stroke();
                }

                var movaDiv = document.getElementById('mova');
                var movbDiv = document.getElementById('movb');
                function moveHandler(evt) {
                    var movaBounds = movaDiv.getBoundingClientRect();
                    var targets = [];
                    if(clicked.length === 2) {
                        if (document.querySelector(".question-area-canvas").classList.contains("resetted")){
                            clicked = []
                            targets = []
                            document.querySelector(".question-area-canvas").classList.remove("resetted")
                        }else{
                        targets = clicked;}
                    } else if(clicked.length === 1) {
                        targets.push(clicked[0]);
                        if(typeof hoverElement !== 'undefined') {
                            targets.push(hoverElement);
                        }
                    }

                    if(targets.length === 2) {

                        var start = eval("("+targets[0].dataset.coord+")")
                        var end =  eval("("+targets[1].dataset.coord+")");
                        drawLineXY(start, end);
                        var movbBounds = movbDiv.getBoundingClientRect();
                        movaDiv.style.left = (start.x - movaBounds.width/2.0)+"px";
                        movaDiv.style.top = (start.y - movaBounds.height/2.0)+"px";
                        movbDiv.style.left = (end.x - movbBounds.width/2.0)+"px";
                        movbDiv.style.top = (end.y - movbBounds.height/2.0)+"px";

                    } else if(targets.length === 1) {


                        var startNearest =  eval("("+targets[0].dataset.coord+")");
                        movaDiv.style.left = (startNearest.x - movaBounds.width/2.0)+"px";
                        movaDiv.style.top = (startNearest.y - movaBounds.height/2.0)+"px";

                        drawLineXY(startNearest, {x:(evt.clientX- document.querySelector(".question-area-canvas").getBoundingClientRect().left), y:(evt.clientY-document.querySelector(".question-area-canvas").getBoundingClientRect().top)});

                    }
                }
                function clickHandler(evt) {
                    if(clicked.length === 2) {
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
                for (var i =0;i<document.querySelectorAll(".match" ).length;i++){
                    attachIds.push(document.querySelectorAll(".match")[i].getAttribute("id"))
                }
                for(var ind = 0; ind < attachIds.length; ++ind) {

                    var el = document.getElementById(attachIds[ind]);
                    el.onclick = clickHandler;
                    el.onmouseover = hoverOverHandler;
                    el.onmouseout = hoverOutHandler;
                }
                window.onmousemove = moveHandler;

            }},
        tracker(){
            let offset = document.querySelector(".question-area").getBoundingClientRect()
            document.addEventListener('mousemove', function(e) {   var pos = {     left: e.clientX-offset.left ,     top: e.clientY -offset.top    }; console.log(pos)})

        }
        },

    mounted() {
        StudentService.GetHomework(2)
            .then((res)=>{
                this.jsonQuestions = res;
                debugger
                this.isLoaded=true
            });
        setTimeout(this.dragger, 1000)
        setTimeout(this.matcher, 1000)


    }


}

</script>
<style scoped>
    .container {
        text-align: center;
    }
    .home{
        background-color: white;

    }
</style>
<style>
    .loading{
        background: url("/images/831.gif") no-repeat;
        height: 60px;
        width: 60px;
    }
    *{
        user-select: none;

    }
    strong{
       color: #5679b3
    }
</style>
