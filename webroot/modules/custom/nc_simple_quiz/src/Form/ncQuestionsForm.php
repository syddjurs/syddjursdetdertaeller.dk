<?php

namespace Drupal\nc_simple_quiz\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

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

    $form['hr'] = array(
      '#markup' => '<hr/>',
    );
    
    foreach ($this->getQuestions() as $question) {
      $form['question' . $question['index']] = $this->buildQuestion($question);
    }

    foreach ($this->getAnswers() as $answer) {
      $form['answer' . $answer['index']] = $this->buildAnswer($answer);
    }

    $form['hr2'] = array(
      '#markup' => '<hr/>',
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
      $form['questions']['answers']['option' . $n] = array(
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
      )
    );

    return $form;
  }

  public function getQuestions() {
    $questions = [];

    $questions[] = [
      'title' => 'Her er et overskrift',
      'text' => 'Og her er en tekst',
      'options' => [
        ['value' => 2, 'text' => 'Spørgsmål 1'],
        ['value' => 1, 'text' => 'Spørgsmål 2'],
      ],
      'color' => 'rgb(251, 52, 73)',
    ];

    foreach ($questions as $index => $question) {
      $questions[$index]['index'] = ($index + 1);
    }

    return $questions;
  }


  public function buildAnswer($answer) {
    $form = array(
      '#type' => 'container',
      '#attributes' => [
        'class' => ['answer'],
        'data-answer' => $answer['index'],
/*        'style' => 'background-color: ' . $answer['color'] . ';',*/

      ]
    );

    $form['text'] = array(
      '#type' => 'item',
      '#title' => $answer['title'],
      '#description' => $answer['text1'],
    );

    $items = [];
    foreach ($answer['bullets'] as $bullet) {
      $items[] = [
        '#markup' => $bullet
      ];
    }
    $form['bullets'] = array(
      '#theme' => 'item_list',
      '#items' => $items,
      '#list_type' => 'ul',
    );

    $markup = $answer['text2'];
    foreach ($answer['placeholders'] as $placeholder => $value) {
      $markup = str_replace($placeholder, $value, $markup);
    }
    $form['text2'] = array(
      '#type' => 'markup',
      '#prefix' => '<p>',
      '#markup' => $markup,
      '#suffix' => '</p>',
    );

    return $form;
  }

  public function getAnswers() {
    $answers = [];

    $answers[] = [
      'title' => 'Svar 1',
      'text1' => '
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc in auctor purus. Vestibulum nec porta libero, id ullamcorper enim. Nulla fringilla tortor eget elit ornare, ut gravida nibh rhoncus. Maecenas cursus, leo a egestas molestie, erat orci hendrerit libero, quis auctor arcu purus sed massa. Praesent et orci aliquet, tempus turpis non, condimentum nisi. Donec fringilla, tellus eget egestas pharetra, libero eros pulvinar lectus, sit amet pharetra ligula diam eu magna. Cras pharetra metus nec porttitor malesuada. Sed sit amet ligula ipsum. Fusce sed consequat neque.',
      'bullets' => [
        'Praesent et orci aliquet',
        'Maecenas cursus',
        'Vestibulum nec porta',
      ],
      'text2' => 'Tekst 2 med :pl',
      'placeholders' => [
        ':pl' => '<strong>placeholder</strong>',
      ],
    ];
    $answers[] = [
      'title' => 'Svar 2',
      'text1' => '
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc in auctor purus. Vestibulum nec porta libero, id ullamcorper enim. Nulla fringilla tortor eget elit ornare, ut gravida nibh rhoncus. Maecenas cursus, leo a egestas molestie, erat orci hendrerit libero, quis auctor arcu purus sed massa. Praesent et orci aliquet, tempus turpis non, condimentum nisi. Donec fringilla, tellus eget egestas pharetra, libero eros pulvinar lectus, sit amet pharetra ligula diam eu magna. Cras pharetra metus nec porttitor malesuada. Sed sit amet ligula ipsum. Fusce sed consequat neque.',
      'bullets' => [
        'Praesent et orci aliquet',
        'Maecenas cursus',
        'Vestibulum nec porta',
      ],
      'text2' => 'Tekst 2 med :pl',
      'placeholders' => [
        ':pl' => '<strong>placeholder</strong>',
      ],
    ];

    foreach ($answers as $index => $answer) {
      $answers[$index]['index'] = ($index + 1);
    }

    return $answers;
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