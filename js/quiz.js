(document).ready(function(){
    $.post("quiz_ajax.php",{over:1},function(info){
        if(info.over == -1){
            //default -> shows the register page with start quiz button
        } else if (info.over == 0){
            $(".quizRegister").hide();
            $('.quiz').fadeIn();
            showQuestion();
        } else {
            // quiz is over and results are to be shown
            $(".quizRegister").hide();
            console.log(info);
            showQuizResults(info.results);
        }

    },'json');
});
$("#startQuiz").click(function(){
    if($('.quizRegister #name').length)
        name = $('.quizRegister #name').val();
    else
        name = 'Guest';
    name = name.length ? name : 'Guest';
    var cat = [];
    $(".quizRegister label input:checked").each(function(){
        cat.push($(this).val());
    });
    if(cat[0] == 0)
        cat = [0];
    cat = cat.join();

    $(".quizRegister").hide();
    $('.quiz').fadeIn();
    showQuestion({nick:nick,category:cat});
});
$("#Qskip").click(function(){
    showQuestion({});
});
$("#Qanswer").click(function(){
    $(".quiz input[type=radio]:checked").each(function(){
        answer = $(this).val();
        showQuestion({answer:answer});
    });
});
$("#Qrestart,#restartQuiz").click(function(){
    $.post('quiz_ajax.php',{restart:1},function(){
        alert("Loading......");
        $('.status').html(' ');
        $(".quizResults").fadeOut();
        $(".quiz").fadeOut();
        $(".quizRegister").slideDown();
    });
});
function showQuizResults(results){
    $('.quiz').hide();
    var m = $(".quizResults");
    $('.quiz .status').clone().appendTo(m);
    m.fadeIn().append("<div class=clearfix></div>");
    c = results.correct;
    w = results.wrong;
    t = results.total;
    m.find('#correct').html(c);
    m.find('#wrong').html(w);
    m.find('#total').html(t);
    m.find('#skipped').html(t-c-w);
    m.find('#time').html(results.time);
    score = 500*(c*2 + w*(-1))/(50+results.time);
    m.find("#score").html(parseInt(score*100)/100);
    if(score>150)
        str="Excellent ! Supergenius !";
    else if(score>100)
        str="Outstanding!! That was awesome !";
    else if(score>75)
        str="You can be proud of yourself.";
    else if(score>50)
        str="Not bad !";
    else if(score>25)
        str="You need to work harder";
    else
        str="Very poor job! Go,improve and then come back !!";

    m.find("#resultMsg").html(str);
}
function showQuestion(params){
    $.post('quiz_ajax.php',params,function(data){
        if(data.results){
	    console.log(data.results);
            /*   $('.quiz .score').html("Displaying Q.no."+ data.results.total +" Correct: "+ data.results.correct
             + " Wrong: <span id=wrong>" + data.results.wrong + "</span> ");
             //.find('#correct').html(data.results.correct);
             */
            var wrong = data.results.wrong;
            var correct = data.results.correct;
            wrong = wrong?wrong:'0';
            correct = correct?correct:'0';
            $('.quiz .score span#wrong').flipcountdown({tick: wrong ,size:'sm'});
            $('.quiz .score span#correct').flipcountdown({tick: correct ,size:'sm'});
            $(".quiz .time").data('count',data.results.time);
            if(data.results.last == 1)
                col = "correct";
            else if (data.results.last == -1)
                col = "wrong";
            else
                col = "";
            if(data.question &&  parseInt(data.results.total)!=1)
            {$(".quiz .status").append("<div class='circle "+ col +"'>"+ parseInt(data.results.total-1)  +"</div>"+
                "<i class='glyphicon glyphicon-arrow-right'></i>");
            } else if(!data.question && parseInt(data.results.total)){
                $(".quiz .status").append("<div class='circle "+ col +"'>"+ parseInt(data.results.total)  +"</div>"+
                    "<i class='glyphicon glyphicon-arrow-right'></i>");
            }
        }
        if(data.question){
            main = $('.quiz');
            main.find('.question').html(data.results.total +". " +  data.question.question);
            options = data.question.options.split("$$");
            h = "";
            for(i=1;i<=options.length;i++){
                h += ("<label><input type=radio name=options value="+ i +" />"+options[i-1]+"</label>");
            }
            main.find('.options').html(h);
            $(function(){
                var i = $('.time').data('count');
                $('.time').flipcountdown({
                    tick:function(){
                        return i++;
                    },
                    size: 'sm'
                });
            });
        } else {
            console.log(data);
            showQuizResults(data.results);
        }
    },'json');
}
