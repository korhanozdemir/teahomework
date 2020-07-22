<template>
    <div class="question-area">
    <div  v-if="this.question" class="question-area-canvas">
        <div class="type1" v-if="question[questionIndex].question_type === 1">
            <div class="empties " >
                <div :key="empty.id" :id="'empty'+empty.id"  class="empty_rect droparea" :style="{ top: coord(empty.bounds)[1]+ 'px', left: coord(empty.bounds)[0] + 'px', width: coord(empty.bounds)[2]  + 'px', height: coord(empty.bounds)[3]  + 'px'  }" v-for="empty in question[questionIndex].drop_targets" >
                    <div class="centered"style="width: 100%" ><strong></strong></div>
                </div></div>
        </div>
        <div class="type2" v-if="question[questionIndex].question_type === 2">
            <div class="lines"  >
                <div class="line" :key="line.id" v-for="line in question[questionIndex].drop_targets"  :style='{ left: + drawLine(line.bounds,line.anchor)[0] + "px", top: + drawLine(line.bounds,line.anchor)[1] + "px", width: + drawLine(line.bounds,line.anchor)[2] + "px",mozTransform:"rotate(" + drawLine(line.bounds,line.anchor)[3] + "deg)", webkitTransform:"rotate("+ drawLine(line.bounds,line.anchor)[3] + "deg)", oTransform:"rotate("+ drawLine(line.bounds,line.anchor)[3] + "deg)", msTransform:"rotate("+drawLine(line.bounds,line.anchor)[3] + "deg)", transform:"rotate("+  drawLine(line.bounds,line.anchor)[3] + "deg)"}'></div>

            </div>
            <div class="dots" >
                <div :key="dot.id" class="dot"  :style="{ top: coord(dot.anchor)[1] + 'px', left: coord(dot.anchor)[0]  + 'px' }" v-for="dot in question[questionIndex].drop_targets" >

                </div>
            </div>
            <div class="empties " >
                <div :key="empty.id" :id="'empty'+empty.id"  class="empty droparea" :style="{ top: coord(empty.bounds)[1]-20 + 'px', left: coord(empty.bounds)[0]-20  + 'px' }" v-for="empty in question[questionIndex].drop_targets" >

                </div></div></div>
        <div class="type3" v-if="question[questionIndex].question_type === 3">
            <button class="button is-primary" @click="clear()">RESET</button>
            <div v-if="this.lines" class="lines">
                <div class="matching-line" :key="line.id[0]+''+line.id[1]" v-for="line in this.lines"  :style='{ left: + drawLine(line.bounds,line.anchor)[0] + "px", top: + drawLine(line.bounds,line.anchor)[1] + "px", width: + drawLine(line.bounds,line.anchor)[2] + "px",mozTransform:"rotate(" + drawLine(line.bounds,line.anchor)[3] + "deg)", webkitTransform:"rotate("+ drawLine(line.bounds,line.anchor)[3] + "deg)", oTransform:"rotate("+ drawLine(line.bounds,line.anchor)[3] + "deg)", msTransform:"rotate("+drawLine(line.bounds,line.anchor)[3] + "deg)", transform:"rotate("+  drawLine(line.bounds,line.anchor)[3] + "deg)"}'></div>
            </div>
            <div class="matchers" >
                <button @click="matcher(match.id)"  :key="match.id" :id="'match'+match.id" :data-coord="'{x:'+coord(match.bounds)[0]+',y:'+coord(match.bounds)[1]+'}'"  class="match" :style="{ top: coord(match.bounds)[1]+ 'px', left: coord(match.bounds)[0] + 'px', width: coord(match.bounds)[2]-10  + 'px', height: coord(match.bounds)[3]-10  + 'px'  }" v-for="match in question[questionIndex].drop_targets" >
                </button></div>
            <div id="mova" style="position:absolute; z-index:100;">&nbsp;</div>
            <div id="movb" style="position:absolute; z-index:100;">&nbsp;</div>
        </div>
        <div class="type4" v-if="question[questionIndex].question_type === 4">
            <div class="checks" >
                <div :key="check.id"  class="check_area"  v-for="check in question[questionIndex].drop_targets" >
                    <button class="check" :id="'check'+check.id"
                            :class="[{ checked: check.checked },{ unchecked: !check.checked }]"
                            @click="check.checked  = !check.checked "
                            :style="{ top: coord(check.bounds)[1]+ 'px', left: coord(check.bounds)[0] + 'px', width: coord(check.bounds)[2]  + 'px', height: coord(check.bounds)[3]  + 'px'  }"></button>
                </div></div>
        </div>
        <div class="child" >
        <img class="img" draggable="false" :style="{ top: coord(image.bounds)[1] + 'px', left: coord(image.bounds)[0]  + 'px', width: coord(image.bounds)[2]  + 'px', height: coord(image.bounds)[3]  + 'px' }" :key="image.id" :src="'images/'+image.image_url" v-for="image in question[questionIndex].images" alt="">
    </div>
    </div>
    <div v-else class="loading centered"></div>
    </div>
</template>

<script>
    import ApiService from "../../services/api.service";
    import StudentService from "../../services/student.service";

    export default {
        name: "questionArea",
        props: ["jsonQuestions","questionIndex","isLoaded","preQuestion"],
        data (){
            return {
                question: this.jsonQuestions,
                answer_model: {
                    option: 3,
                    question_index: 1,
                    option_matched: true,
                    time : 2
                },
                answers:{},
                targets:{},
                temporary_target:0,
                lines:[
                ]
            }
        },
        methods: {
            coord(coordinates){
                return  coordinates.replace("{","").replace("}","").split(",")
            },
            drawLine(coords, coords2){
                    // bottom right
                if(this.question[this.questionIndex].question_type === 3){
                    var x1 = Number(this.coord(coords)[0])+11
                    var y1 = Number(this.coord(coords)[1])+11
                    // top right
                    var x2 = Number(this.coord(coords2)[0])+11
                    var y2 = Number(this.coord(coords2)[1])+11}
                else{
                    var x1 = Number(this.coord(coords)[0])
                    var y1 = Number(this.coord(coords)[1])
                    // top right
                    var x2 = Number(this.coord(coords2)[0])+4
                    var y2 = Number(this.coord(coords2)[1])+4
                }
                    // distance
                    var length = Math.sqrt(((x2-x1) * (x2-x1)) + ((y2-y1) * (y2-y1)));
                    // center
                    var cx = ((x1 + x2) / 2) - (length / 2);
                    var cy = ((y1 + y2) / 2) - (2 / 2);

                    var angle = Math.atan2((y1-y2),(x1-x2))*(180/Math.PI);

                    return [cx,cy,length,angle]
                    //
                },
            matcher(id){
                let same_line = true;
                if(this.temporary_target === 0){
                    this.temporary_target = id
                }
                else {
                    if(typeof this.targets["match"+id] !== "undefined"){

                        if( this.targets["match"+id].includes("match"+this.temporary_target )){
                            same_line = false
                    }}
                    if(typeof this.targets["match"+this.temporary_target] !== "undefined" ){

                        if(this.targets["match"+this.temporary_target].includes("match"+id)){
                            same_line = false
                        }}
                    if (this.temporary_target === id || !same_line){
                        this.temporary_target = 0
                    }
                    else{
                        if(this.targets["match"+id]){
                            this.targets["match"+id].push("match"+this.temporary_target)
                            this.lines.push({id:[id,this.temporary_target],bounds:document.querySelector("#match"+id).dataset.coord.replace("y:","").replace("x:",""),anchor:document.querySelector("#match"+this.temporary_target).dataset.coord.replace("y:","").replace("x:","")})
                            this.temporary_target = 0
                        }
                        else{
                            this.targets["match"+id] = []
                            this.targets["match"+id].push("match"+this.temporary_target)
                            this.lines.push({id:[id,this.temporary_target],bounds:document.querySelector("#match"+id).dataset.coord.replace("y:","").replace("x:",""),anchor:document.querySelector("#match"+this.temporary_target).dataset.coord.replace("y:","").replace("x:","")})
                            this.temporary_target = 0
                        }
                    }
                }


            },
            clear(){
                this.targets = {}
                this.lines = []
                this.temporary_target = 0;
                if(document.querySelector('.question-area-canvas canvas')){
                    const elem = document.querySelectorAll('.question-area-canvas canvas');
                    for(let i=0;i<elem.length;i++){
                        document.querySelector('.question-area-canvas').removeChild(elem[i])
                    }
                }document.querySelector('.question-area-canvas').classList.add("resetted")

            },
            setData(question_id){
                let questionResult = []
                if(this.question[this.questionIndex].question_type === 0){
                    debugger
                    questionResult.push({answer:document.querySelector(".chosen").getAttribute("id").replace("index","")})
                }
                StudentService.UpdateAllQuestionResult(2,question_id, questionResult)
                    .then((res)=>{
                        console.log("done");
                    })
            },
            getData(i){
                StudentService.GetAllQuestionResult(2,i+1)
                    .then((res)=>{
                        this.answers = res.data;
                        if(this.answers.length>0){
                            this.useData()
                        }
                    });

            },
            useData(){
                if(this.answers[0].type === 1){
                    console.log(1)
                }
            }
            }
        ,watch: {
            // whenever question changes, this function will run
            jsonQuestions: function () {
                this.question =this.jsonQuestions.data.questions
            },
            questionIndex: function () {
                this.getData(this.questionIndex)

            }


        }
    }
</script>

<style scoped>
    .child{
    }
    button{
        border: none;
        z-index: 1;
        position: absolute;
        top: 486px;
        left: 491px;
        border-radius: 6px;
    }
    .match{
        background: url("/images/liconlar/V2MatchingOptionDraggable.png") no-repeat;
        position: absolute;
        z-index: 1;
        cursor: pointer;
    }
    canvas{
     position: absolute;
     top: 0;
     left: 0;
     z-index: 1;
    }
    .check{
        position: absolute;
    }
    svg{
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0
    }

    .centered {

        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .unchecked{
        background: url("/images/GridUnchecked.png") no-repeat;
    }
    .checked{
        background: url("/images/GridChecked.png") no-repeat;
    }

    .line{
        line-height:1px;
        position:absolute;
        padding:0;
        margin:0;
        background-color: black;
        height: 2px;
        z-index: 3;
    }
    .matching-line{
        line-height:1px;
        position:absolute;
        padding:0;
        margin:0;
        background-color: black;
        height: 3px;
        z-index: 1;
    }
    .empty{
         background: url("/images/liconlar/matching_option_image_empty.png") no-repeat;
         position: absolute;
         height: 45px;
         width: 200px;
         z-index: 4;
         }
     .empty_rect{
         position: absolute;
         z-index: 4;
         border: 2px solid black;

     }
    .child .img{
        position: absolute;

    }
    .question-area{
        border: 1px solid gray ;
        position: relative;

    }
    .question-area-canvas{
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

    .dot{
        position: absolute;
        top: 233px;
        left: 269px;
        width: 8px;
        height: 8px;
        background-color: black;
        border-radius: 8px;
        z-index: 2;


    }

</style>
