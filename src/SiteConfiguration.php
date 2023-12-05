<?php

namespace Drupal\ucb_site_configuration;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\ucb_site_configuration\Entity\ExternalServiceInclude;

/**
 * The Site Configuration service contains functions used by the module.
 */
class SiteConfiguration {
  use StringTranslationTrait;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type repository.
   *
   * @var \Drupal\Core\Entity\EntityTypeRepositoryInterface
   */
  protected $entityTypeRepository;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRouteMatch;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a UserInviteHelperService.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Extension\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Extension\TranslationManager $string_translation
   *   The translation manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityTypeRepositoryInterface $entity_type_repository
   *   The entity type repository.
   * @param \Drupal\Core\Routing\RouteMatchInterface $current_route_match
   *   The current route match.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(
        AccountInterface $user,
        ModuleHandlerInterface $module_handler,
        ConfigFactoryInterface $config_factory,
        TranslationManager $string_translation,
        EntityTypeManagerInterface $entity_type_manager,
        EntityTypeRepositoryInterface $entity_type_repository,
        RouteMatchInterface $current_route_match,
        MessengerInterface $messenger
  ) {
    $this->user = $user;
    $this->moduleHandler = $module_handler;
    $this->configFactory = $config_factory;
    $this->stringTranslation = $string_translation;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeRepository = $entity_type_repository;
    $this->currentRouteMatch = $current_route_match;
    $this->messenger = $messenger;
  }

  /**
   * Gets the machine name of the CU Boulder base theme to configure.
   *
   * @return string
   *   The machine name of the CU Boulder base theme to configure.
   */
  public function getThemeName() {
    return 'boulder_base';
  }

  /**
   * Builds the theme settings form.
   *
   * @param array &$form
   *   The form build array.
   * @param \Drupal\Core\Form\FormStateInterface &$form_state
   *   The form state.
   */
  public function buildThemeSettingsForm(array &$form, FormStateInterface &$form_state) {
    // Accessing these theme settings is no longer possible from the Drupal
    // default theme settings.
    if ($this->currentRouteMatch->getRouteName() != 'ucb_site_configuration.appearance_form') {
      $this->messenger->addMessage($this->t('Please visit <a href="@url">CU Boulder site settings → Appearance</a> to customize the appearance of this site.', ['@url' => Url::fromRoute('ucb_site_configuration.appearance_form')->toString()]));
      return;
    }

    $themeName = $this->getThemeName();

    $form['header'] = [
      '#type' => 'details',
      '#title' => 'Site header and navigation',
      '#open' => TRUE,
    ];

    $form['header']['ucb_campus_header_color'] = [
      '#type'           => 'select',
      '#title'          => $this->t('CU Boulder campus header color'),
      '#default_value'  => theme_get_setting('ucb_campus_header_color', $themeName),
      '#options'        => [
        $this->t('Black'),
        $this->t('White'),
      ],
      '#description'    => $this->t('Select the color for the header background for the campus branding information at the top of the page.'),
    ];

    $form['header']['ucb_header_color'] = [
      '#type'           => 'select',
      '#title'          => $this->t('CU Boulder site header color'),
      '#default_value'  => theme_get_setting('ucb_header_color', $themeName),
      '#options'        => [
        $this->t('Black'),
        $this->t('White'),
        $this->t('Light Gray'),
        $this->t('Dark Gray'),
      ],
      '#description'    => $this->t('Select the color for the header background for the site information at the top of the page.'),
    ];

    $form['header']['ucb_menu_style'] = [
      '#type'           => 'select',
      '#title'          => $this->t('Menu style'),
      '#default_value'  => theme_get_setting('ucb_menu_style', $themeName) ?? 'default',
      '#options'        => [
        'default' => $this->t('Default'),
        'highlight'  => $this->t('Highlight'),
        'ivory'  => $this->t('Ivory'),
        'layers'  => $this->t('Layers'),
        'minimal'  => $this->t('Minimal'),
        'modern'  => $this->t('Modern'),
        'rise'  => $this->t('Rise'),
        'simple'  => $this->t('Simple'),
        'shadow'  => $this->t('Shadow'),
        'spirit'  => $this->t('Spirit'),
        'swatch'  => $this->t('Swatch'),
        'tradition'  => $this->t('Tradition'),
      ],
      '#description'    => $this->t('Select a style for the main navigation menu.'),
    ];

    $form['header']['ucb_breadcrumb_nav'] = [
      '#type'           => 'checkbox',
      '#title'          => $this->t('Show breadcrumb navigation on pages'),
      '#default_value'  => theme_get_setting('ucb_breadcrumb_nav', $themeName),
      '#description'    => $this->t('If enabled, the breadcrumb navigation will be shown at the top of pages, helping visitors find their way around the site.'),
    ];

    $form['header']['ucb_sticky_menu'] = [
      '#type'           => 'checkbox',
      '#title'          => $this->t('Show sticky menu'),
      '#default_value'  => theme_get_setting('ucb_sticky_menu', $themeName),
      '#description'    => $this->t('The sticky menu appears at the top of the page when scrolling on large-screen devices, allowing for quick access to links.'),
    ];

    $form['header']['ucb_secondary_menu_position'] = [
      '#type'           => 'select',
      '#title'          => $this->t('Position of the secondary menu'),
      '#default_value'  => theme_get_setting('ucb_secondary_menu_position', $themeName),
      '#options'        => [
        'above'  => $this->t('Above the main navigation'),
        'inline' => $this->t('Inline with the main navigation'),
      ],
      '#description'    => $this->t('The secondary menu of this site can be populated with secondary or action links and displayed inline with or above the main navigation.'),
    ];

    $form['header']['ucb_secondary_menu_button_display'] = [
      '#type'           => 'checkbox',
      '#title'          => $this->t('Display links in the secondary menu as buttions'),
      '#default_value'  => theme_get_setting('ucb_secondary_menu_button_display', $themeName),
      '#description'    => $this->t('Check this box to display the links in the secondary menu of this site as buttons instead of links.'),
    ];

    $form['content'] = [
      '#type' => 'details',
      '#title' => 'Page content',
      '#open' => TRUE,
    ];

    $form['content']['ucb_heading_font'] = [
      '#type'           => 'select',
      '#title'          => $this->t('Heading font'),
      '#default_value'  => theme_get_setting('ucb_heading_font', $themeName) ?? 'bold',
      '#options'        => [
        'bold' => $this->t('Bold'),
        'normal' => $this->t('Normal'),
      ],
      '#description'    => $this->t('Headings are bold by default, but can also be set to the same font weight as normal text.'),
    ];

    $form['misc'] = [
      '#type' => 'details',
      '#title' => 'Miscellaneous',
      '#open' => TRUE,
    ];

    $form['misc']['ucb_sidebar_position'] = [
      '#type'           => 'select',
      '#title'          => $this->t('Where to show sidebar content on a page'),
      '#default_value'  => theme_get_setting('ucb_sidebar_position', $themeName),
      '#options'        => [
        $this->t('Left'),
        $this->t('Right'),
      ],
      '#description'    => $this->t('Select if sidebar content should appear on the left or right side of a page.'),
    ];

    // Choose where social share buttons are positioned on each page.
    $form['misc']['ucb_social_share_position'] = [
      '#type'           => 'select',
      '#title'          => $this->t('Where your social media sharing links render'),
      '#default_value'  => theme_get_setting('ucb_social_share_position', $themeName),
      '#options'        => [
        $this->t('None'),
        $this->t('Left Side (Desktop) / Below Title (Mobile)'),
        $this->t('Left Side (Desktop) / Below Content (Mobile)'),
        $this->t('Below Content'),
        $this->t('Below Title'),
      ],
      '#description'    => $this->t('Select the location for social sharing links (Facebook, Twitter, etc) to appear on your pages.'),
    ];

    if ($this->user->hasPermission('edit ucb site advanced')) {
      $form['advanced'] = [
        '#type'  => 'details',
        '#title' => $this->t('Advanced'),
        '#open'  => FALSE,
      ];
      $form['advanced']['ucb_rave_alerts'] = [
        '#type'           => 'checkbox',
        '#title'          => $this->t('Show campus-wide alerts'),
        '#default_value'  => theme_get_setting('ucb_rave_alerts', $themeName),
        '#description'    => $this->t('If enabled, campus-wide alerts will be displayed at the top of the site.'),
      ];
      $form['advanced']['ucb_be_boulder'] = [
        '#type'           => 'select',
        '#title'          => $this->t('Where to display the Be Boulder slogan on the site.'),
        '#default_value'  => theme_get_setting('ucb_be_boulder', $themeName),
        '#options'        => [
          $this->t('None'),
          $this->t('Footer'),
          $this->t('Header'),
        ],
        '#description'    => $this->t('Check this box if you would like to display the "Be Boulder" slogan in the header.'),
      ];
      $form['advanced']['ucb_secondary_menu_default_links'] = [
        '#type'           => 'checkbox',
        '#title'          => $this->t('Display the standard Boulder secondary menu in the header navigation region.'),
        '#default_value'  => theme_get_setting('ucb_secondary_menu_default_links', $themeName),
        '#description'    => $this->t('Check this box if you would like to display the default Boulder secondary menu links in the header.'),
      ];
      $form['advanced']['ucb_footer_menu_default_links'] = [
        '#type'           => 'checkbox',
        '#title'          => $this->t('Display the standard Boulder menus in the footer region.'),
        '#default_value'  => theme_get_setting('ucb_footer_menu_default_links', $themeName),
        '#description'    => $this->t('Check this box if you would like to display the default Boulder footer menu links in the footer.'),
      ];
    }
  }

  /**
   * Gets the external services options.
   *
   * @return array
   *   The external services options available when creating an external
   *   service include.
   */
  public function getExternalServicesOptions() {
    $externalServicesConfiguration = $this->getConfiguration()->get('external_services') ?? [];
    $options = [];
    foreach ($externalServicesConfiguration as $externalServiceName => $externalServiceConfiguration) {
      $options[$externalServiceName] = $externalServiceConfiguration['label'];
    }
    return $options;
  }

  /**
   * Attaches site information.
   *
   * This function is meant to be called from `hook_preprocess`.
   *
   * @param array &$variables
   *   The array to add the site information to.
   */
  public function attachSiteInformation(array &$variables) {
    $configuration = $this->getConfiguration();
    $settings = $this->getSettings();
    $siteTypeOptions = $configuration->get('site_type_options');
    $siteAffiliationOptions = $configuration->get('site_affiliation_options');
    $variables['site_name'] = $this->configFactory->get('system.site')->get('name');
    $siteTypeId = $settings->get('site_type');
    $variables['site_type'] = [
      'id' => $siteTypeId,
      'label' => $siteTypeId && isset($siteTypeOptions[$siteTypeId]) ? $siteTypeOptions[$siteTypeId]['label'] : $siteTypeId,
    ];
    $siteAffiliationId = $settings->get('site_affiliation');
    $siteAffiliationAttribs = $siteAffiliationId == 'custom' ? [
      'label' => $settings->get('site_affiliation_label'),
      'url' => $settings->get('site_affiliation_url'),
    ] : ($siteAffiliationId && isset($siteAffiliationOptions[$siteAffiliationId]) ? $siteAffiliationOptions[$siteAffiliationId] : [
      'label' => $siteAffiliationId ?? '',
      'url' => '',
    ]);
    $variables['site_affiliation'] = array_merge(['id' => $siteAffiliationId], $siteAffiliationAttribs);
    $siteSearchOptions = $configuration->get('site_search_options');
    $siteSearchEnabled = $settings->get('site_search_enabled');
    $variables['site_search'] = [];
    foreach ($siteSearchEnabled as $name) {
      $siteSearchOption = $siteSearchOptions[$name];
      $siteSearchOption['name'] = $name;
      if ('custom' == $name) {
        $searchLabel = $settings->get('site_search_label');
        if ($searchLabel) {
          $siteSearchOption['label'] = $searchLabel;
        }
        $searchPlaceholder = $settings->get('site_search_placeholder');
        if ($searchPlaceholder) {
          $siteSearchOption['placeholder'] = $searchPlaceholder;
        }
        $searchUrl = $settings->get('site_search_url');
        if ($searchUrl) {
          $siteSearchOption['url'] = $searchUrl;
        }
      }
      $variables['site_search'][] = $siteSearchOption;
    }
  }

  /**
   * Attaches configuration to Articles.
   *
   * @param array &$variables
   *   The array to add the site information to.
   */
  public function attachArticlesConfiguration(array &$variables) {
    $settings = $this->getSettings();
    $variables['article_date_format'] = $settings->get('article_date_format') ?? '0';
    $variables['related_articles_exclude_categories'] = $settings->get('related_articles_exclude_categories') ?? [];
    $variables['related_articles_exclude_tags'] = $settings->get('related_articles_exclude_tags') ?? [];
  }

  /**
   * Attaches configuration to People Lists.
   *
   * @param array &$variables
   *   The array to add the site information to.
   */
  public function attachPeopleListConfiguration(array &$variables) {
    $settings = $this->getSettings();
    $variables['people_list_filter_1_label'] = $settings->get('people_list_filter_1_label') ?? 'Filter 1';
    $variables['people_list_filter_2_label'] = $settings->get('people_list_filter_1_label') ?? 'Filter 2';
    $variables['people_list_filter_3_label'] = $settings->get('people_list_filter_1_label') ?? 'Filter 3';
  }

  /**
   * Attaches external service includes.
   *
   * This function is meant to be called from `hook_preprocess`. Variables can
   * be referenced from the template using `service_servicename_includes`.
   *
   * @param array &$variables
   *   The array to add the external service includes to.
   * @param \Drupal\node\NodeInterface|null $node
   *   A node to match includes that are for specific content. If null, only
   *   sitewide includes will be attached.
   */
  public function attachExternalServiceIncludes(array &$variables, NodeInterface $node = NULL) {
    $storage = $this->entityTypeManager->getStorage($this->entityTypeRepository->getEntityTypeFromClass(ExternalServiceInclude::class));
    $query = $storage->getQuery('OR')->condition('sitewide', TRUE);
    if ($node) {
      $query->condition('nodes.*', $node->id());
    }
    $results = $query->execute();
    /** @var \Drupal\ucb_site_configuration\Entity\ExternalServiceIncludeInterface[] */
    $externalServiceIncludeEntities = $storage->loadMultiple($results);
    $externalServiceIncludeArrays = [];
    foreach ($externalServiceIncludeEntities as $externalServiceInclude) {
      $externalServiceName = $externalServiceInclude->getServiceName();
      $externalServiceIncludeArrays[$externalServiceName][] = [
        'id' => $externalServiceInclude->id(),
        'label' => $externalServiceInclude->label(),
        'service_name' => $externalServiceName,
        'service_settings' => $externalServiceInclude->getServiceSettings(),
        'sitewide' => $externalServiceInclude->isSitewide(),
      ];
    }
    foreach ($externalServiceIncludeArrays as $externalServiceName => $externalServiceIncludeArray) {
      $variables['service_' . $externalServiceName . '_includes'] = $externalServiceIncludeArray;
    }
  }

  /**
   * Gets the external service includes available on content form pages.
   *
   * @return \Drupal\ucb_site_configuration\Entity\ExternalServiceIncludeInterface[]
   *   All the external service includes that can be added and removed from the
   *   content form pages.
   */
  public function getContentAccessibleExternalServiceIncludes() {
    $storage = $this->entityTypeManager->getStorage($this->entityTypeRepository->getEntityTypeFromClass(ExternalServiceInclude::class));
    $query = $storage->getQuery('AND')->condition('sitewide', FALSE);
    if (!$this->user->hasPermission('administer ucb external services')) {
      $query->condition('content_editing_enabled', TRUE);
    }
    return $storage->loadMultiple($query->execute());
  }

  /**
   * Gets the static configuration.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   The static configuration of the CU Boulder Site Configuration module.
   */
  public function getConfiguration() {
    return $this->configFactory->get('ucb_site_configuration.configuration');
  }

  /**
   * Gets the user-modifiable settings.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   The user-modifiable settings of the CU Boulder Site Configuration module.
   */
  public function getSettings() {
    return $this->configFactory->get('ucb_site_configuration.settings');
  }

}
