<?php

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Routing\RequestContext;
use Drupal\Core\Session\AccountInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The form for the "General" tab in CU Boulder site settings.
 */
class GeneralForm extends ConfigFormBase {

  /**
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The path validator.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * The request context.
   *
   * @var \Drupal\Core\Routing\RequestContext
   */
  protected $requestContext;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * The site configuration service defined in this module.
   *
   * @var \Drupal\ucb_site_configuration\SiteConfiguration
   */
  protected $service;

  /**
   * Constructs a GeneralForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator.
   * @param \Drupal\Core\Routing\RequestContext $request_context
   *   The request context.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   * @param \Drupal\ucb_site_configuration\SiteConfiguration $service
   *   The site configuration service defined in this module.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AliasManagerInterface $alias_manager, PathValidatorInterface $path_validator, RequestContext $request_context, AccountInterface $user, SiteConfiguration $service) {
    parent::__construct($config_factory);
    $this->aliasManager = $alias_manager;
    $this->pathValidator = $path_validator;
    $this->requestContext = $request_context;
    $this->user = $user;
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
      $container->get('path_alias.manager'),
      $container->get('path.validator'),
      $container->get('router.request_context'),
      $container->get('current_user'),
      $container->get('ucb_site_configuration')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['system.site', 'ucb_site_configuration.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ucb_site_configuration_general_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $configuration = $this->service->getConfiguration();
    $settings = $this->service->getSettings();
    $systemSiteSettings = $this->config('system.site');
    $siteTypeOptions = $configuration->get('site_type_options');
    $siteAffiliationOptions = array_filter($configuration->get('site_affiliation_options'), function ($value) {
      return !$value['type_restricted'];
    });
    $form['site_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site name'),
      '#default_value' => $systemSiteSettings->get('name'),
      '#required' => TRUE,
    ];
    $form['site_frontpage'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Home page'),
      '#default_value' => $this->aliasManager->getAliasByPath($systemSiteSettings->get('page.front')),
      '#required' => TRUE,
      '#size' => 40,
      '#description' => $this->t('Specify a relative URL to display as the site home page.'),
      '#field_prefix' => $this->requestContext->getCompleteBaseUrl(),
    ];
    if ($this->user->hasPermission('edit ucb site advanced')) {
      $advanced = [
        '#type'  => 'details',
        '#title' => $this->t('Advanced'),
        '#open'  => FALSE,
      ];
      $advanced['site_type'] = [
        '#type' => 'select',
        '#title' => $this->t('Type'),
        '#default_value' => $settings->get('site_type'),
        '#options' => array_merge(['' => $this->t('- None -')], array_map(function ($value) {
            return $value['label'];
        }, $siteTypeOptions)),
        '#required' => FALSE,
      ];
      $affiliationHidesOn = [];
      foreach ($siteTypeOptions as $siteTypeId => $siteType) {
        if (isset($siteType['affiliation'])) {
          array_push($affiliationHidesOn, [':input[name="site_type"]' => ['value' => $siteTypeId]]);
        }
      }
      $advanced['site_affiliation_container'] = [
        '#type' => 'container',
        '#states' => [
          'invisible' => $affiliationHidesOn,
        ],
        'site_affiliation' => [
          '#type' => 'select',
          '#title' => $this->t('Affiliation'),
          '#default_value' => $settings->get('site_affiliation'),
          '#options' => array_merge(['' => $this->t('- None -')], array_map(function ($value) {
              return $value['label'];
          }, $siteAffiliationOptions), ['custom' => $this->t('Custom')]),
          '#required' => FALSE,
        ],
        'site_affiliation_custom' => [
          '#type' => 'fieldset',
          '#title' => $this->t('Custom affiliation'),
          '#description' => $this->t('Define a title and optional URL for the custom affiliation.'),
          '#states' => [
            'visible' => [[':input[name="site_affiliation"]' => ['value' => 'custom']]],
          ],
          'site_affiliation_label' => [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#default_value' => $settings->get('site_affiliation_label'),
            '#required' => FALSE,
            '#maxlength' => 255,
          ],
          'site_affiliation_url' => [
            '#type' => 'textfield',
            '#title' => $this->t('URL'),
            '#default_value' => $settings->get('site_affiliation_url'),
            '#required' => FALSE,
            '#maxlength' => 255,
          ],
        ],
      ];
      $siteSearchOptions = $configuration->get('site_search_options');
      $siteSearchEnabled = $settings->get('site_search_enabled');
      $advanced['site_search_enabled'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Enable searching'),
      ];
      foreach ($siteSearchOptions as $key => $value) {
        $advanced['site_search_enabled']['site_search_enabled_' . $key] = [
          '#type' => 'checkbox',
          '#title' => $value['label'],
          '#default_value' => in_array($key, $siteSearchEnabled),
        ];
      }
      $advanced['site_search'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Site search'),
        '#states' => [
          'visible' => [[':input[name="site_search_enabled_custom"]' => ['checked' => TRUE]]],
        ],
      ];
      $advanced['site_search']['site_search_label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Label'),
        '#default_value' => $settings->get('site_search_label'),
        '#placeholder' => $siteSearchOptions['custom']['label'],
        '#required' => FALSE,
        '#size' => 32,
        '#description' => $this->t('Leave blank to use the default label.'),
      ];
      $advanced['site_search']['site_search_placeholder'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Placeholder'),
        '#default_value' => $settings->get('site_search_placeholder'),
        '#placeholder' => $siteSearchOptions['custom']['placeholder'],
        '#required' => FALSE,
        '#size' => 32,
        '#description' => $this->t('Leave blank to use the default placeholder.'),
      ];
      $advanced['site_search']['site_search_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Search page'),
        '#default_value' => $settings->get('site_search_url') ? $this->aliasManager->getAliasByPath($settings->get('site_search_url')) : '',
        '#states' => [
          'required' => [[':input[name="site_search_enabled_custom"]' => ['checked' => TRUE]]],
        ],
        '#size' => 40,
        '#description' => $this->t('Specify a relative URL to use as the site search page.'),
        '#field_prefix' => $this->requestContext->getCompleteBaseUrl(),
      ];
      $form['advanced'] = $advanced;
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\system\Form\SiteInformationForm::validateForm
   *   Contains the validation of a home page path.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (($value = $form_state->getValue('site_frontpage')) && $value[0] !== '/') {
      $form_state->setErrorByName('site_frontpage', $this->t("The path '%path' has to start with a slash.", ['%path' => $form_state->getValue('site_frontpage')]));
    }
    if (!$this->pathValidator->isValid($form_state->getValue('site_frontpage'))) {
      $form_state->setErrorByName('site_frontpage', $this->t("The path '%path' is invalid.", ['%path' => $form_state->getValue('site_frontpage')]));
    }
    if ($this->user->hasPermission('edit ucb site advanced') && $form_state->getValues('site_search_enabled')['site_search_enabled_custom']) {
      if (($value = $form_state->getValue('site_search_url')) && $value[0] !== '/') {
        $form_state->setErrorByName('site_search_url', $this->t("The path '%path' has to start with a slash.", ['%path' => $form_state->getValue('site_search_url')]));
      }
      if (!$this->pathValidator->isValid($form_state->getValue('site_search_url'))) {
        $form_state->setErrorByName('site_search_url', $this->t("The path '%path' is invalid.", ['%path' => $form_state->getValue('site_search_url')]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $configuration = $this->service->getConfiguration();
    $siteTypeOptions = $configuration->get('site_type_options');
    $this->config('system.site')
      ->set('name', $form_state->getValue('site_name'))
      ->set('page.front', $form_state->getValue('site_frontpage'))
      ->save();
    if ($this->user->hasPermission('edit ucb site advanced')) {
      $siteTypeId = $form_state->getValue('site_type');
      $siteAffiliationId = $form_state->getValue('site_affiliation');
      if ($siteTypeId && isset($siteTypeOptions[$siteTypeId]) && isset($siteTypeOptions[$siteTypeId]['affiliation'])) {
        $siteAffiliationId = $siteTypeOptions[$siteTypeId]['affiliation'];
      }
      $siteSearchEnabled = [];
      $siteSearchEnabledFormValues = $form_state->getValues('site_search_enabled');
      $siteSearchFormValues = $form_state->getValues('site_search');
      foreach ($configuration->get('site_search_options') as $key => $value) {
        if ($siteSearchEnabledFormValues['site_search_enabled_' . $key]) {
          $siteSearchEnabled[] = $key;
        }
      }
      $this->config('ucb_site_configuration.settings')
        ->set('site_type', $siteTypeId)
        ->set('site_affiliation', $siteAffiliationId)
        ->set('site_affiliation_label', $form_state->getValue('site_affiliation_label'))
        ->set('site_affiliation_url', $form_state->getValue('site_affiliation_url'))
        ->set('site_search_enabled', $siteSearchEnabled)
        ->set('site_search_label', $siteSearchFormValues['site_search_label'])
        ->set('site_search_placeholder', $siteSearchFormValues['site_search_placeholder'])
        ->set('site_search_url', $siteSearchFormValues['site_search_url'])
        ->save();
    }
    parent::submitForm($form, $form_state);
  }

}
