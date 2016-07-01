(function ($, Drupal) {
  /**
   * Add new command for reading a message.
   */
  Drupal.AjaxCommands.prototype.setQuizResult = function (ajax, response, status) {
    // Place content in current-msg div.
    $('#ncquestionsform .quiz-container').once('show-result').fadeOut(200,function(){
      var result = $('<div>').hide().addClass('.result').html('<h3>'+response.title+'</h3>');
      $(this).after(result);
      $.each(response.content,function(){
        result.append('<p><b>' + this.title + '</b></p>')
          .append('<p>' + this.text + '</p>');
      });
      result.fadeIn(200);
    });
  }
})(jQuery, Drupal);

var nc_question = 1;
var nc_question_finished = 1;
var nc_questions = 1;
var nc_answers = [];

jQuery(document).ready(
  function () {
    var $ = $ || jQuery;
    jQuery('.question .form-checkbox').prop('checked', false);
    jQuery('.question[data-question=1]').show();
    jQuery('.question').each(function (index, item) {
      var question = jQuery(item).data('question');
      if (question > nc_questions) nc_questions = question;
      nc_answers[question] = 0;
    });
    jQuery('.question .form-checkbox').click(function (event) {
      var question = jQuery(event.target).closest('.question').data('question');
      jQuery('.question[data-question=' + question + '] .form-checkbox').prop('checked', false);
      jQuery(event.target).prop('checked', true);
      nc_answers[question] = jQuery(event.target).val();
    });
    jQuery('.question .form-submit').click(function (event) {
      var question = jQuery(event.target).closest('.question').data('question');
      event.preventDefault();
      if (nc_answers[question] > 0) {
        var offset = $(this).closest('section.simple-quiz').offset(); // Contains .top and .left

        offset.left -= 20;
        offset.top -= 20;

        var adminMenuHeight = $('.toolbar-bar').height() + $('.toolbar-tray-horizontal.is-active').height();

        $('html, body').animate({
          scrollTop: offset.top - adminMenuHeight,
          scrollLeft: offset.left - adminMenuHeight
        },200);

        nc_question = question + 1;
        if (nc_question_finished < question) nc_question_finished = question;
        if (question < nc_questions) {
          jQuery('.question[data-question=' + question + ']').hide();
          jQuery('.question[data-question=' + nc_question + ']').show();
        } else {
          // var highest_answer = 1;
          // var correct_answer = 1;
          // var answers = [];
          // for(var i = 1; i <= nc_questions; i++) {
          //     answers[nc_answers[i]] = 0;
          //     if (highest_answer < nc_answers[i]) highest_answer = nc_answers[i];
          // }
          // for(var i = 1; i <= nc_questions; i++) {
          //     answers[nc_answers[i]]++;
          // }
          // var record = 0;
          // for(var i = 1; i <= highest_answer; i++) {
          //     if (answers[i] > record) {
          //         record = answers[i];
          //         correct_answer = i;
          //     }
          // }
          // jQuery('.answer[data-answer=' + correct_answer + ']').show();
          jQuery('.submit').once('submitted').click();
        }
        var percentage = Math.round((100 / nc_questions) * nc_question_finished);
        jQuery('.progress .progress__percentage').html(percentage + '%');
        jQuery('.progress .progress__bar').css({'width': percentage + '%'});
      }
    });
  }
);
