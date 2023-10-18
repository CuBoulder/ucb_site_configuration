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
 *
 * While "Pages" could eventually be split off into its own tab, the settings
 * are found under "General", at least for now. "General" and "Pages" require
 * different permissions to access.
 */
class GeneralForm extends ConfigFormBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

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
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator.
   * @param \Drupal\Core\Routing\RequestContext $request_context
   *   The request context.
   * @param \Drupal\ucb_site_configuration\SiteConfiguration $service
   *   The site configuration service defined in this module.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AccountInterface $user, AliasManagerInterface $alias_manager, PathValidatorInterface $path_validator, RequestContext $request_context, SiteConfiguration $service) {
    parent::__construct($config_factory);
    $this->user = $user;
    $this->aliasManager = $alias_manager;
    $this->pathValidator = $path_validator;
    $this->requestContext = $request_context;
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
      $container->get('current_user'),
      $container->get('path_alias.manager'),
      $container->get('path.validator'),
      $container->get('router.request_context'),
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
   *
   * @see \Drupal\system\Form\SiteInformationForm::buildForm
   *   Contains the definition of a home page field.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $configuration = $this->service->getConfiguration();
    $settings = $this->service->getSettings();
    $systemSiteSettings = $this->config('system.site');
    if ($this->user->hasPermission('edit ucb site general')) {
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
      $form['site_type'] = [
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
      $form['site_affiliation_container'] = [
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
    }
    if ($this->user->hasPermission('edit ucb site pages')) {
      $form['site_frontpage'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Home page'),
        '#default_value' => $this->aliasManager->getAliasByPath($systemSiteSettings->get('page.front')),
        '#required' => TRUE,
        '#size' => 40,
        '#description' => $this->t('Specify a relative URL to display as the home page.'),
        '#field_prefix' => $this->requestContext->getCompleteBaseUrl(),
      ];
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
    if ($this->user->hasPermission('edit ucb site pages')) {
      if (($value = $form_state->getValue('site_frontpage')) && $value[0] !== '/') {
        $form_state->setErrorByName('site_frontpage', $this->t("The path '%path' has to start with a slash.", ['%path' => $form_state->getValue('site_frontpage')]));
      }
      if (!$this->pathValidator->isValid($form_state->getValue('site_frontpage'))) {
        $form_state->setErrorByName('site_frontpage', $this->t("The path '%path' is invalid.", ['%path' => $form_state->getValue('site_frontpage')]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $configuration = $this->service->getConfiguration();
    if ($this->user->hasPermission('edit ucb site general')) {
      $siteTypeOptions = $configuration->get('site_type_options');
      $this->config('system.site')->set('name', $form_state->getValue('site_name'))->save();
      $siteTypeId = $form_state->getValue('site_type');
      $siteAffiliationId = $form_state->getValue('site_affiliation');
      if ($siteTypeId && isset($siteTypeOptions[$siteTypeId]) && isset($siteTypeOptions[$siteTypeId]['affiliation'])) {
        $siteAffiliationId = $siteTypeOptions[$siteTypeId]['affiliation'];
      }
      $this->config('ucb_site_configuration.settings')
        ->set('site_type', $siteTypeId)
        ->set('site_affiliation', $siteAffiliationId)
        ->set('site_affiliation_label', $form_state->getValue('site_affiliation_label'))
        ->set('site_affiliation_url', $form_state->getValue('site_affiliation_url'))
        ->save();
    }
    if ($this->user->hasPermission('edit ucb site pages')) {
      $this->config('system.site')->set('page.front', $form_state->getValue('site_frontpage'))->save();
    }
    parent::submitForm($form, $form_state);
  }

}
