<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\SiteConfiguration.
 */

namespace Drupal\ucb_site_configuration;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\node\NodeInterface;

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
	 * Constructs a UserInviteHelperService.
	 *
	 * @param \Drupal\Core\Session\AccountInterface $user
	 *   The current user.
	 * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
	 *   The module handler.
	 * @param \Drupal\Core\Extension\ConfigFactoryInterface $config_factory
	 *   The config factory.
	 * @param \Drupal\Core\Extension\TranslationManager $stringTranslation
	 *   The translation manager.
	 */
	public function __construct(
		AccountInterface $user,
		ModuleHandlerInterface $module_handler,
		ConfigFactoryInterface $config_factory,
		TranslationManager $stringTranslation
	) {
		$this->user = $user;
		$this->moduleHandler = $module_handler;
		$this->configFactory = $config_factory;
		$this->stringTranslation = $stringTranslation;
	}

	/**
	 * @return string
	 *   The machine name of the CU Boulder base theme to configure.
	 */
	public function getThemeName() {
		return 'boulderD9_base';
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
		$themeName = $this->getThemeName();

		$form['ucb_campus_header_color'] = [
			'#type'           => 'select',
			'#title'          => $this->t('CU Boulder campus header color'),
			'#default_value'  => theme_get_setting('ucb_campus_header_color', $themeName),
			'#options'        => [
				$this->t('Black'),
				$this->t('White')
			],
			'#description'    => $this->t('Select the color for the header background for the campus branding information at the top of the page.')
		];

		$form['ucb_header_color'] = [
			'#type'           => 'select',
			'#title'          => $this->t('CU Boulder site header color'),
			'#default_value'  => theme_get_setting('ucb_header_color', $themeName),
			'#options'        => [
				$this->t('Black'),
				$this->t('White'),
				$this->t('Light'),
				$this->t('Dark')
			],
			'#description'    => $this->t('Select the color for the header background for the site information at the top of the page.')
		];

		$form['ucb_sidebar_position'] = [
			'#type'           => 'select',
			'#title'          => $this->t('Where to show sidebar content on a page'),
			'#default_value'  => theme_get_setting('ucb_sidebar_position', $themeName),
			'#options'        => [
				$this->t('Left'),
				$this->t('Right')
			],
			'#description'    => $this->t('Select if sidebar content should appear on the left or right side of a page.')
		];

		$form['ucb_be_boulder'] = [
			'#type'           => 'select',
			'#title'          => $this->t('Where to display the Be Boulder slogan on the site.'),
			'#default_value'  => theme_get_setting('ucb_be_boulder', $themeName),
			'#options'        => [
				$this->t('None'),
				$this->t('Footer'),
				$this->t('Header')
			],
			'#description'    => $this->t('Check this box if you would like to display the "Be Boulder" slogan in the header.')
		];

		$form['ucb_rave_alerts'] = [
			'#type'           => 'checkbox',
			'#title'          => $this->t('Show campus-wide alerts'),
			'#default_value'  => theme_get_setting('ucb_rave_alerts', $themeName),
			'#description'    => $this->t('If enabled, campus-wide alerts will be displayed at the top of the site.')
		];

		$form['ucb_breadcrumb_nav'] = [
			'#type'           => 'checkbox',
			'#title'          => $this->t('Show breadcrumb navigation on pages'),
			'#default_value'  => theme_get_setting('ucb_breadcrumb_nav', $themeName),
			'#description'    => $this->t('If enabled, the breadcrumb navigation will be shown at the top of pages, helping visitors find their way around the site.')
		];

		$form['ucb_gtm_account'] = [
			'#type'           => 'textfield',
			'#title'          => $this->t('GTM Account Number'),
			'#default_value'  => theme_get_setting('ucb_gtm_account', $themeName),
			'#description'    => $this->t('Google Tag Manager account number e.g. GTM-123456.'),
		];

		$form['ucb_secondary_menu_default_links'] = [
			'#type'           => 'checkbox',
			'#title'          => $this->t('Display the standard Boulder secondary menu in the header navigation region.'),
			'#default_value'  => theme_get_setting('ucb_secondary_menu_default_links', $themeName),
			'#description'    => $this->t('Check this box if you would like to display the default Boulder secondary menu links in the header.')
		];

		$form['ucb_secondary_menu_position'] = [
			'#type'           => 'select',
			'#title'          => $this->t('Position of the secondary menu'),
			'#default_value'  => theme_get_setting('ucb_secondary_menu_position', $themeName),
			'#options'        => [
				'inline' => $this->t('Inline with the main navigation'),
				'above'  => $this->t('Above the main navigation')
			],
			'#description'    => $this->t('The secondary menu of this site can be populated with secondary or action links and displayed inline with or above the main navigation.')
		];

		$form['ucb_secondary_menu_button_display'] = [
			'#type'           => 'checkbox',
			'#title'          => $this->t('Display links in the secondary menu as buttions'),
			'#default_value'  => theme_get_setting('ucb_secondary_menu_button_display', $themeName),
			'#description'    => $this->t('Check this box to display the links in the secondary menu of this site as buttons instead of links.')
		];

		$form['ucb_footer_menu_default_links'] = [
			'#type'           => 'checkbox',
			'#title'          => $this->t('Display the standard Boulder menus in the footer region.'),
			'#default_value'  => theme_get_setting('ucb_footer_menu_default_links', $themeName),
			'#description'    => $this->t('Check this box if you would like to display the default Boulder footer menu links in the footer.')
		];
		// Choose where social share buttons are positioned on each page
		$form['ucb_social_share_position'] = [
			'#type'           => 'select',
			'#title'          => $this->t('Where your social media sharing links render'),
			'#default_value'  => theme_get_setting('ucb_social_share_position', $themeName),
			'#options'        => [
				$this->t('None'),
				$this->t('Left Side (Desktop) / Below Title (Mobile)'),
				$this->t('Left Side (Desktop) / Below Content (Mobile)'),
				$this->t('Below Content'),
				$this->t('Below Title')
			],
			'#description'    => $this->t('Select the location for social sharing links (Facebook, Twitter, etc) to appear on your pages.')
		];
		// Choose date/time format sitewide
		$form['ucb_date_format'] = [
			'#type'           => 'select',
			'#title'          => $this->t('Display settings for Date formats on Articles'),
			'#default_value'  => theme_get_setting('ucb_date_format', $themeName),
			'#options'        => [
				$this->t('Short Date'),
				$this->t('Medium Date'),
				$this->t('Long Date'),
				$this->t('Short Date with Time'),
				$this->t('Medium Date with Time'),
				$this->t('Long Date with Time'),
				$this->t('None - Hide')
			],
			'#description'    => $this->t('Select the preferred Global Date/Time format for dates on your site.')
		];
	}

	/**
	 * Builds the inner settings form for an external service on a node add or edit page.
	 * 
	 * @param array &$form
	 *   The form build array.
	 * @param string $externalServiceName
	 *   The machine name of the external srvice.
	 * @param \Drupal\node\NodeInterface $node
	 *   The node for which this form is being built.
	 * @param FormStateInterface $form_state
	 *   The current state of the form.
	 */
	public function buildExternalServiceContentSettingsForm(array &$form, $externalServiceName, NodeInterface $node, FormStateInterface $form_state) {
		switch ($externalServiceName) {
			case 'mainstay':
				$form['ucb_external_service_' . $externalServiceName . '__college_id'] = [
					'#type' => 'textfield',
					'#size' => '60',
					'#title' => $this->t('College ID'),
					// '#default_value' => $node->get('ucb_external_service_' . $externalServiceName . '__college_id')
				];
			break;
			default:
		}
	}

	public function getExternalServicesOptions() {
		$externalServicesConfiguration = $this->getConfiguration()->get('external_services');
		$options = [];
		foreach ($externalServicesConfiguration as $externalServiceName => $externalServiceConfiguration)
			$options[$externalServiceName] = $externalServiceConfiguration['label'];
		return $options;
	}

	/**
	 * @return array
	 *   The external services options available on content nodes.
	 */
	public function getContentExternalServicesOptions() {
		$externalServicesConfiguration = $this->getConfiguration()->get('external_services') ?? [];
		$externalServicesSettings = $this->getSettings()->get('external_services') ?? [];
		$options = [];
		foreach ($externalServicesSettings as $externalServiceName => $externalServiceSettings) {
			if($externalServiceSettings['enabled'] === 'some')
				$options[$externalServiceName] = $externalServicesConfiguration[$externalServiceName]['content_label'];
		}
		return $options;
	}

	/**
	 * @return \Drupal\Core\Config\ImmutableConfig
	 *   The configuration of the CU Boulder Site Configuration module.
	 */
	public function getConfiguration() {
		return $this->configFactory->get('ucb_site_configuration.configuration');
	}

	/**
	 * @return \Drupal\Core\Config\ImmutableConfig
	 *   The user-modifiable settings of the CU Boulder Site Configuration module.
	 */
	public function getSettings() {
		return $this->configFactory->get('ucb_site_configuration.settings');
	}
}
