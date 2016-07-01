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

    $form['title'] = array(
      '#prefix' => '<h2>',
//      '#markup' => $this->t('What kind of Syddjurs type are you?'),
      '#markup' => 'Hvilken Syddjurstype er du?',
      '#suffix' => '</h2>',
    );

    $form['description'] = array(
      '#prefix' => '<p><i>',
//      '#markup' => $this->t('Prøv vores helt uvidenskabelige test, hvis du er nysgerrig efter en indikation af, hvor i Syddjurs din familiedrøm allerbedst udfolder sig. Hvis ingen svar passer perfekt, så vælg det, der passer bedst. Testen tager cirka syv minutter.'),
      '#markup' => 'Prøv vores helt uvidenskabelige test, hvis du er nysgerrig efter en indikation af, hvor i Syddjurs din familiedrøm allerbedst udfolder sig. Hvis ingen svar passer perfekt, så vælg det, der passer bedst. Testen tager cirka syv minutter.',
      '#suffix' => '</i></p>',
    );

    $form['quiz'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => 'quiz-container',
      ),
    );

    foreach ($this->getQuestions() as $question) {
      $form['quiz']['question' . $question['index']] = $this->buildQuestion($question);
    }

    $form['quiz']['hidden_submit'] = array(
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
          'message' => $this->t('Calculating quiz result...'),
        ),
      ],
    );

    $form['quiz']['progress'] = array(
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
        'class' => ['button', 'button--secondary']
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
      'title' => 'Resultat',
      'content' => [],
    ];

    $response = [];

    foreach($questions as $question){
      if(!empty($answers[$question['index']])){
        $answer = $answers[$question['index']];
        foreach($question['options'] as $option){
          if($option['value'] == $answer){
            foreach($option['area'] AS $area){
              if(empty($response[$area])){
                $response[$area] = 0;
              }
              $response[$area]++;
            }
          }
        }
      }
    }
    arsort($response);

    $count = null;
    foreach($response as $key => $val){
      if(!is_null($count) && $val < $count){
        continue;
      }
      $result = $this->getQuizResultOption($key);
      if(!empty($result)){
        $message->content[] = $result;
        $count = $val;
      }
    }

    return $message;
  }

  private function getQuizResultOption($key=''){
    $options = [];
    $options['land'] = [
      'title' => 'Landområder',
      'text' => 'Syddjurs byder på mange, skønne landsbyer som Nimtofte i Nord, Knebel i Syd, Lime i Vest og Balle i Øst, hvor I vil føle jer godt hjemme. Her rækker pengene ekstra langt til hus og have, frihed og luft til tæerne. Afstanden til Aarhus er ikke afgørende for dig, og du er ikke fremmed over for tanken om bil(er). Den varierede natur, årstidernes skiften og mulighed for at dyrke egne råvarer gør hele forskellen.',
    ];
    $options['ebeltoft'] = [
      'title' => 'Ebeltoft',
      'text' => 'Ebeltoftområdet byder på den købstadsidyl, maritime lækkerhed og kulturelle overflod, som matcher din familie godt. Med Nationalpark Mols Bjerge i baghaven og Ebeltoft Vig lige uden for døren får du let adgang til din skattede natur. I Syddjurs’ største by kan du dyrke din lidenskab for smagfulde specialbutikker – og Aarhus er stadig kun 45 minutter væk.',
    ];
    $options['kalø'] = [
      'title' => 'Kalø-Mols',
      'text' => 'Som pendlerfamilie passer Kalø-Mols-området rigtig godt til din familie. Du er kun en halv time fra Aarhus, samtidig med at du har et enestående kyst- og kulturlandskab lige foran dig. Du kan både dyrke nærværet i parcelhuskvarterets trygge rammer og give den gas med de utallige outdoor-muligheder.',
    ];
    $options['letbane'] = [
      'title' => 'Letbanebyer',
      'text' => 'De gamle stationsbyer er perfekte til jeres familie. Her er alle byens servicemuligheder kombineret med den effektive logistik til Aarhus, som kun bliver forbedret med Letbanen. Samtidig er der rig mulighed for at dyrke engagementet i det lokale og fællesskabet – så kom, og få det til at ske!',
    ];
    return !empty($options[$key]) ? $options[$key] : null;
  }

  public function getQuestions() {
    $questions = [];

    $questions[] = [
      'title' => 'Spørgsmål 1',
      'text' => 'Pengene rækker langt i Syddjurs, og du finder skønne boliger her i alle prisklasser. Hvad tænker du, jeres kommende bolig cirka må koste?',
      'options' => [
        ['value' => 1, 'text' => 'Max 1 mio. kr', 'area' => ['land']],
        ['value' => 2, 'text' => '1-2 mio. kr.', 'area' => ['letbane']],
        ['value' => 3, 'text' => '2-3 mio. kr.', 'area' => ['ebeltoft']],
        ['value' => 4, 'text' => 'Over 3 mio. kr.', 'area' => ['kalø']],
      ],
    ];

    $questions[] = [
      'title' => 'Spørgsmål 2',
      'text' => 'Måske drømmer du om den helt store villa med havudsigt eller et nedlagt landbrug med lærkesang og jord til. Du finder hele paletten i Syddjurs. Hvilken slags bolig står mon øverst på jeres ønskeseddel?',
      'options' => [
        ['value' => 1, 'text' => 'Landejendom', 'area' => ['land']],
        ['value' => 2, 'text' => 'Kollektiv', 'area' => ['land']],
        ['value' => 3, 'text' => 'Parcelhus', 'area' => ['land','letbane','ebeltoft','kalø']],
        ['value' => 4, 'text' => 'Lejlighed', 'area' => ['ebeltoft']],
        ['value' => 5, 'text' => 'Nybyggeri', 'area' => ['letbane']],
        ['value' => 6, 'text' => 'Villa', 'area' => ['kalø']],
      ],
    ];

    $questions[] = [
      'title' => 'Spørgsmål 3',
      'text' => 'Syddjurs har en rig, varieret natur – mere end 40 af de 60 typer oprindelig, vild natur i Danmark er repræsenteret hos os. Hvad vil du gerne have som udsigt fra køkkenvinduet?',
      'options' => [
        ['value' => 1, 'text' => 'Vandet og det dramatiske kystlandskab – mindre kan ikke gøre det', 'area' => ['kalø']],
        ['value' => 2, 'text' => 'Letbanen, skov og grønne områder – grønt er godt for øjet, men mobilitet er vigtigst', 'area' => ['letbane']],
        ['value' => 3, 'text' => 'Legende børn på vejen – dét er livet', 'area' => ['land','letbane','ebeltoft','kalø']],
        ['value' => 4, 'text' => 'Enge, marker og bakkedrag – og gerne et krondyr i ny og næ', 'area' => ['land']],
        ['value' => 5, 'text' => 'Kysten og et levende havnemiljø – vi elsker det maritime!', 'area' => ['ebeltoft']],
      ],
    ];

    $questions[] = [
      'title' => 'Spørgsmål 4',
      'text' => 'Der er fin forbindelse mellem Aarhus og Syddjurs med bil, bus og snart med Letbanen. Men tidsforbruget afhænger selvfølgelig af, hvor i Syddjurs I slår jer ned. Hvor lang tid må det maksimalt tage dig at komme til Aarhus N?',
      'options' => [
        ['value' => 1, 'text' => '20-30 minutter', 'area' => ['kalø']],
        ['value' => 2, 'text' => '25-35 minutter', 'area' => ['letbane']],
        ['value' => 3, 'text' => '45 minutter', 'area' => ['ebeltoft']],
        ['value' => 4, 'text' => 'Det er ikke vigtigt for mig', 'area' => ['land']],
      ],
    ];

    $questions[] = [
      'title' => 'Spørgsmål 5',
      'text' => 'Hverdagslogistikken er ofte central, når man skal flytte. Hvordan kommer vi på arbejde? Til fritidsinteresser og indkøb? Hvilket transportbehov forstiller I jer?',
      'options' => [
        ['value' => 1, 'text' => 'To biler', 'area' => ['ebeltoft','land']],
        ['value' => 2, 'text' => 'En bil', 'area' => ['kalø']],
        ['value' => 3, 'text' => 'Udelukkende offentlig transport, cykel og gå-ben', 'area' => ['letbane']],
      ],
    ];

    $questions[] = [
      'title' => 'Spørgsmål 6',
      'text' => 'Du tror det måske ikke, men uanset hvor mainstream eller skæv en type, du er – så er der nogen præcis som dig i Syddjurs, som ville elske at have dig som nabo. Hvilken type kan du bedst identificere dig med?',
      'options' => [
        ['value' => 1, 'text' => '<b><u>Bonderøven</u>:</b> Masser af plads, lidt høns og mulighed for at flikke en shelter sammen. Du dyrker yoga og måske lidt spelt. Mon et selvforsynende kollektiv er næste skridt?', 'area' => ['land']],
        ['value' => 2, 'text' => '<b><u>Gør-det-selv-typen</u>:</b> Planen er at købe et håndværkertilbud, du kan ombygge. Du drømmer om at banke et værksted op, så du kan rode med veteranbilen.', 'area' => ['land']],
        ['value' => 3, 'text' => '<b><u>Kulturtripper</u>:</b> Kunst, historiske markeder og børnekultur – du tænder på det hele. Uanset om vi taler spoken word, kammermusik eller den nyeste Woody Allen, er du på pletten.', 'area' => ['ebeltoft']],
        ['value' => 4, 'text' => '<b><u>Fællesskabs-fan</u>:</b> Du elsker at kunne låne en kop sukker, snakke over hækken og arrangere vejfest. Du ender lynhurtigt i diverse frivilliggrupper, fordi du simpelthen ikke kan lade være med at engagere dig lokalt.', 'area' => ['letbane']],
        ['value' => 5, 'text' => '<b><u>Parcelhuselsker</u>:</b> Dit hjerte frydes ved lukkede villaveje med oplyste stisystemer direkte til den lokale skole. Din favorithygge er arbejdsdag i grundejerforeningen.', 'area' => ['kalø']],
        ['value' => 6, 'text' => '<b><u>Liebhaver</u>:</b> En eksklusiv bolig, der matcher din karriere, med panoramaudsigt over vandet og plads til Audierne. Du dyrker det gode liv med gourmetmad, ferniseringer og lækker natur.', 'area' => ['ebeltoft']],
        ['value' => 7, 'text' => '<b><u>Outdoor-freaken</u>:</b> Mountainbiken, surfbrættet og løbeskoene er dine redskaber – naturen er din arena. Du elsker, at stor set alle friluftsmuligheder er lige uden for døren.', 'area' => ['kalø']],
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