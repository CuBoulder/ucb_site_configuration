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
      '#default_value' => $this->toAliasPathIfValid($systemSiteSettings->get('page.front')),
      '#required' => TRUE,
      '#size' => 40,
      '#description' => $this->t('Specify a relative URL to display as the site home page.'),
      '#field_prefix' => $this->requestContext->getCompleteBaseUrl(),
    ];
    $form['site_404'] = [
      '#type' => 'textfield',
      '#title' => $this->t('404 (not found) page'),
      '#default_value' => $this->toAliasPathIfValid($systemSiteSettings->get('page.404')),
      '#size' => 40,
      '#description' => $this->t('This page is displayed when no other content matches the requested document.'),
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
      $siteSearchUrl = $settings->get('site_search_url');
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
        '#default_value' => $siteSearchUrl && $siteSearchUrl[0] === '/' ? $this->aliasManager->getAliasByPath($siteSearchUrl) : $siteSearchUrl,
        '#states' => [
          'required' => [[':input[name="site_search_enabled_custom"]' => ['checked' => TRUE]]],
        ],
        '#size' => 40,
        '#description' => $this->t('Specify a relative URL to use as the site search page.'),
        '#field_prefix' => $this->requestContext->getCompleteBaseUrl(),
      ];
      $advanced['gtm_account'] = [
        '#type'           => 'textfield',
        '#title'          => $this->t('GTM Account Number'),
        '#default_value'  => $settings->get('gtm_account'),
        '#description'    => $this->t('Google Tag Manager account number e.g. GTM-123456.'),
      ];
      $form['advanced'] = $advanced;
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * Validates a path to a page on the site.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $fieldName
   *   The name of the form field containing the path to validate.
   *
   * @return bool
   *   Whether the form field contains a valid path.
   */
  protected function validatePath(FormStateInterface $form_state, $fieldName) {
    $value = $form_state->getValue($fieldName);
    if ($value) {
      if ($value[0] !== '/') {
        $form_state->setErrorByName($fieldName, $this->t("The path '%path' has to start with a slash.", ['%path' => $value]));
        return FALSE;
      }
      if ($this->pathValidator->isValid($value)) {
        return TRUE;
      }
      $form_state->setErrorByName($fieldName, $this->t("The path '%path' is invalid.", ['%path' => $value]));
    }
    return FALSE;
  }

  /**
   * Converts the path to an alias path if valid, otherwise returns the path.
   *
   * @param string $path
   *   The path to convert.
   *
   * @return string
   *   The converted path.
   */
  protected function toAliasPathIfValid($path) {
    return $path[0] === '/' ? $this->aliasManager->getAliasByPath($path) : $path;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\system\Form\SiteInformationForm::validateForm
   *   Contains the validation of a home page path.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($this->validatePath($form_state, 'site_frontpage')) {
      $form_state->setValue('site_frontpage', $this->aliasManager->getPathByAlias($form_state->getValue('site_frontpage')));
    }
    if ($this->validatePath($form_state, 'site_404')) {
      $form_state->setValue('site_404', $this->aliasManager->getPathByAlias($form_state->getValue('site_404')));
    }
    if ($this->user->hasPermission('edit ucb site advanced') && $form_state->getValues('site_search_enabled')['site_search_enabled_custom']) {
      $this->validatePath($form_state, 'site_search_url');
    }
    parent::validateForm($form, $form_state);
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
      ->set('page.404', $form_state->getValue('site_404'))
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
        ->set('gtm_account', $form_state->getValue('gtm_account'))
        ->save();
    }
    parent::submitForm($form, $form_state);
  }

}
