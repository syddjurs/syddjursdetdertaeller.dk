<?php

namespace Drupal\nc_simple_quiz\Ajax;
use Drupal\Core\Ajax\CommandInterface;

/**
 * Created by PhpStorm.
 * User: mma
 * Date: 08/06/2016
 * Time: 10.34
 */
class SetQuizResult implements CommandInterface {
  protected $message;

  // Constructs a ReadMessageCommand object.
  public function __construct($message) {
    $this->message = $message;
  }

  // Implements Drupal\Core\Ajax\CommandInterface:render().
  public function render() {
    return array(
      'command' => 'setQuizResult',
      'title' => $this->message->title,
      'content' => $this->message->content,
    );
  }
}
