<?php

namespace Drupal\nc_simple_quiz\Form;

//use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
//use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\nc_simple_quiz\Ajax\SetQuizResult;

class ncQuestionsForm extends FormBase {
  public function getFormId() {
    return 'ncQuestionsForm';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [
      '#attached' => array(
        'library' => array(
          'nc_simple_quiz/nc_questions'
        ),
      ),
    ];

    $form['nc_settings'] = array(
      '#type' => 'vertical_tabs',
      '#title' => 'Signup'
    );

    foreach ($this->getQuestions() as $question) {
      $form['question' . $question['index']] = $this->buildQuestion($question);
    }

    $form['hidden_submit'] = array(
      '#type' => 'button',
      '#attributes' => array(
        'class' => ['submit'],
        'style' => 'display: none;',
      ),
      '#ajax' => [
        'callback' => array($this, 'getQuizResult'),
        'event' => 'click',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('Calculating quiz result...'),
        ),
      ],
    );

    $form['progress'] = array(
      '#theme' => 'progress_bar',
      '#percent' => '0',
      '#label' => '',
      '#description' => '',
    );

    return $form;
  }

  public function buildQuestion($question) {
    $form = array(
      '#type' => 'container',
      '#attributes' => [
        'class' => ['question'],
        'data-question' => $question['index'],
//        'style' => 'background-color: ' . $question['color'] . ';',
      ]
    );

    $form['text'] = array(
      '#type' => 'item',
      '#title' => $question['title'],
      '#description' => $question['text'],
    );

    $form['questions'] = array(
      '#type' => 'fieldset',
    );


    $n = 1;

    foreach ($question['options'] as $option) {
      $form['questions']['answers']['option-' . $question['index'] . '-' . $n] = array(
        '#type' => 'checkbox',
        '#title' => $option['text'],
        '#return_value' => $option['value'],
        '#default_value' => 0,
      );

      $n++;
    }

    $form['next'] = array(
      '#type' => 'button',
      '#value' => t('Next'),
      '#attributes' => array(
        'class' => ['btn', 'btn-md', 'btn-outline-inverse']
      ),
    );

    return $form;
  }

  public function getQuizResult($form, FormStateInterface $form_state){
    $response = new AjaxResponse();

    $answers = [];
    foreach($form_state->getValues() as $key => $val){
      if(preg_match('/^option-/',$key)){
        if(!empty($val)){
          $option = explode('-',$key);
          $answer = array_pop($option);
          $question = array_pop($option);

          $answers[$question] = $val;
        }
      }
    }

    $message = $this->calculateQuizResult($answers);
    $response->addCommand(new SetQuizResult($message));
    return $response;
  }

  private function calculateQuizResult($answers){
    $questions = $this->getQuestions();

    $message = (object) [
      'title' => 'Det virker',
      'content' => '',
    ];

    $reponse = [];

    foreach($questions as $question){
      if(!empty($answers[$question['index']])){
        $answer = $answers[$question['index']];
        foreach($question['options'] as $option){
          if($option['value'] == $answer){
            $reponse[$question['index']] = (object) [
              'question' => (object) [
                'title' => $question['title'],
                'text' => $question['text'],
              ],
              'answer' => (object) $option,
            ];
          }
        }
      }
    }

    $message->content .= var_export($reponse,true);

    return $message;
  }

  public function getQuestions() {
    $questions = [];

    $questions[] = [
      'title' => 'Spørgsmål 1',
      'text' => 'Hvem er vi?',
      'options' => [
        ['value' => 2, 'text' => 'Svar 1'],
        ['value' => 1, 'text' => 'Svar 2'],
      ],
    ];

    $questions[] = [
      'title' => 'Spørgsmål 2',
      'text' => 'Hvor kommer vi fra?',
      'options' => [
        ['value' => 2, 'text' => 'Svar 3'],
        ['value' => 1, 'text' => 'Svar 4'],
      ],
    ];

    $questions[] = [
      'title' => 'Spørgsmål 3',
      'text' => 'Hvor kan vi stille de tomme flasker?',
      'options' => [
        ['value' => 2, 'text' => 'Svar 5'],
        ['value' => 1, 'text' => 'Svar 6'],
      ],
    ];

    foreach ($questions as $index => $question) {
      $questions[$index]['index'] = ($index + 1);
    }

    return $questions;
  }
  
  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }
}