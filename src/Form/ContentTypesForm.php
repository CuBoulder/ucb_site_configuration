<?php

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The form for the "Content" tab in CU Boulder site settings.
 */
class ContentTypesForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The site configuration service defined in this module.
   *
   * @var \Drupal\ucb_site_configuration\SiteConfiguration
   */
  protected $service;

  /**
   * Constructs a ContentTypesForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\ucb_site_configuration\SiteConfiguration $service
   *   The site configuration service defined in this module.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entityTypeManager, SiteConfiguration $service) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entityTypeManager;
    $this->service = $service;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container that allows getting any needed services.
   *
   * @link https://www.drupal.org/node/2133171 For more on dependency injection
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('ucb_site_configuration')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ucb_site_configuration.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ucb_site_configuration_pages_form';
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\system\Form\SiteInformationForm::buildForm
   *   Contains the definition of a home page field.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->service->getSettings();
    $entityStorage = $this->entityTypeManager->getStorage('taxonomy_term');
    $categoryTerms = $entityStorage->loadByProperties(['vid' => 'category']);
    $categoryOptions = [];
    $tagTerms = $entityStorage->loadByProperties(['vid' => 'tags']);
    $tagOptions = [];
    foreach ($categoryTerms as $categoryTerm) {
      $categoryOptions[$categoryTerm->id()] = $categoryTerm->label();
    }
    foreach ($tagTerms as $tagTerm) {
      $tagOptions[$tagTerm->id()] = $tagTerm->label();
    }
    $form['article'] = [
      '#type'  => 'details',
      '#title' => $this->t('Article'),
      '#open'  => TRUE,
      'enabled_by_default' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable related articles by default for new articles'),
        '#description' => $this->t('If enabled, related articles will default to on when creating a new article. A content author may still turn on or off related articles manually for an individual article.'),
        '#default_value' => $settings->get('related_articles_enabled_by_default') ?? FALSE,
        '#required' => FALSE,
      ],
      'exclude_categories' => [
        '#type' => 'checkboxes',
        '#title' => $this->t('Exclude categories'),
        '#default_value' => $settings->get('related_articles_exclude_categories') ?? [],
        '#options' => $categoryOptions,
        '#required' => FALSE,
      ],
      'exclude_tags' => [
        '#type' => 'checkboxes',
        '#title' => $this->t('Exclude tags'),
        '#default_value' => $settings->get('related_articles_exclude_tags') ?? [],
        '#options' => $tagOptions,
        '#required' => FALSE,
      ],
    ];
    $form['people_list'] = [
      '#type'  => 'details',
      '#title' => $this->t('People List Page'),
      '#open'  => TRUE,
      'people_list_filter_1_label' => [
        '#type'           => 'textfield',
        '#title'          => $this->t('Filter 1 label'),
        '#default_value'  => $settings->get('people_list_filter_1_label') ?? 'Filter 1',
        '#description'    => $this->t('Choose the label that will be used for "Filter 1" on People List Pages.'),
      ],
      'people_list_filter_2_label' => [
        '#type'           => 'textfield',
        '#title'          => $this->t('Filter 2 label'),
        '#default_value'  => $settings->get('people_list_filter_2_label') ?? 'Filter 2',
        '#description'    => $this->t('Choose the label that will be used for "Filter 2" on People List Pages.'),
      ],
      'people_list_filter_3_label' => [
        '#type'           => 'textfield',
        '#title'          => $this->t('Filter 3 label'),
        '#default_value'  => $settings->get('people_list_filter_3_label') ?? 'Filter 3',
        '#description'    => $this->t('Choose the label that will be used for "Filter 3" on People List Pages.'),
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ucb_site_configuration.settings')
      ->set('related_articles_enabled_by_default', $form_state->getValue('enabled_by_default'))
      ->set('related_articles_exclude_categories', array_keys(array_filter($form_state->getValue('exclude_categories'))))
      ->set('related_articles_exclude_tags', array_keys(array_filter($form_state->getValue('exclude_tags'))))
      ->set('people_list_filter_1_label', $form_state->getValue('people_list_filter_1_label'))
      ->set('people_list_filter_2_label', $form_state->getValue('people_list_filter_2_label'))
      ->set('people_list_filter_3_label', $form_state->getValue('people_list_filter_3_label'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
