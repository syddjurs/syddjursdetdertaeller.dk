<?php
/**
 * Created by PhpStorm.
 * User: pve
 * Date: 19/01/2016
 * Time: 10.36
 */

namespace Drupal\nc_cookieinfo\Form;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class NcCookieInfoSettingsForm extends ConfigFormBase  {

  public function getFormId() {
    return 'nc_cookieinfo_settings';
  }

  protected function getEditableConfigNames() {
    return [
      'nc_cookieinfo.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('nc_cookieinfo.settings');

    $form['cookie_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cookie info title'),
      '#description' => $this->t('Add a title to the cookie info popup. This is optional'),
      '#default_value' => $config->get('cookie_title')
    ];

    $form['cookie_body'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Cookie info text'),
      '#description' => $this->t('Main cookie info text'),
      '#default_value' => $config->get('cookie_body')['value'],
      '#format' => $config->get('cookie_body')['format'],
      '#required' => TRUE
    ];

    $form['cookie_button'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cookie info button'),
      '#description' => $this->t('Text to show in the cookie info dismiss button ex. "OK" or "Close"'),
      '#default_value' => $config->get('cookie_button'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('nc_cookieinfo.settings')
      ->set('cookie_title', $form_state->getValue('cookie_title'))
      ->set('cookie_body', $form_state->getValue('cookie_body'))
      ->set('cookie_button', $form_state->getValue('cookie_button'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}