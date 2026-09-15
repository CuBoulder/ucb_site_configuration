<?php

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * The form for the "Newsletter email (MJML)" tab in CU Boulder site settings
 */
class NewsletterMjmlForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ucb_site_configuration_newsletter_mjml_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ucb_site_configuration.newsletter_mjml'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ucb_site_configuration.newsletter_mjml');

    $form['api_endpoint'] = [
      '#type' => 'url',
      '#title' => $this->t('MJML render endpoint'),
      '#default_value' => $config->get('api_endpoint') ?: 'https://api.mjml.io/v1/render',
      '#description' => $this->t('The MJML render API URL. Defaults to the official MJML API (https://api.mjml.io/v1/render).'),
      '#required' => TRUE,
    ];

    $form['application_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MJML application ID'),
      '#default_value' => $config->get('application_id'),
      '#description' => $this->t('The application ID (username) issued by the MJML API.'),
    ];

    $form['secret_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MJML secret key'),
      '#default_value' => $config->get('secret_key'),
      '#description' => $this->t('The secret key (password) issued by the MJML API.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ucb_site_configuration.newsletter_mjml')
      ->set('api_endpoint', $form_state->getValue('api_endpoint'))
      ->set('application_id', $form_state->getValue('application_id'))
      ->set('secret_key', $form_state->getValue('secret_key'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
