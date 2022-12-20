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
use Drupal\node\NodeInterface;

class SiteConfiguration {

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
	 */
	public function __construct(
		AccountInterface $user,
		ModuleHandlerInterface $module_handler,
		ConfigFactoryInterface $config_factory
	) {
		$this->user = $user;
		$this->moduleHandler = $module_handler;
		$this->configFactory = $config_factory;
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
			'#title'          => t('CU Boulder campus header color'),
			'#default_value'  => theme_get_setting('ucb_campus_header_color', $themeName),
			'#options'        => [
				t('Black'),
				t('White')
			],
			'#description'    => t('Select the color for the header background for the campus branding information at the top of the page.')
		];

		$form['ucb_header_color'] = [
			'#type'           => 'select',
			'#title'          => t('CU Boulder site header color'),
			'#default_value'  => theme_get_setting('ucb_header_color', $themeName),
			'#options'        => [
				t('Black'),
				t('White'),
				t('Light'),
				t('Dark')
			],
			'#description'    => t('Select the color for the header background for the site information at the top of the page.')
		];

		$form['ucb_sidebar_position'] = [
			'#type'           => 'select',
			'#title'          => t('Where to show sidebar content on a page'),
			'#default_value'  => theme_get_setting('ucb_sidebar_position', $themeName),
			'#options'        => [
				t('Left'),
				t('Right')
			],
			'#description'    => t('Select if sidebar content should appear on the left or right side of a page.')
		];

		$form['ucb_be_boulder'] = [
			'#type'           => 'select',
			'#title'          => t('Where to display the Be Boulder slogan on the site.'),
			'#default_value'  => theme_get_setting('ucb_be_boulder', $themeName),
			'#options'        => [
				t('None'),
				t('Footer'),
				t('Header')
			],
			'#description'    => t('Check this box if you would like to display the "Be Boulder" slogan in the header.')
		];

		$form['ucb_rave_alerts'] = [
			'#type'           => 'checkbox',
			'#title'          => t('Show campus-wide alerts'),
			'#default_value'  => theme_get_setting('ucb_rave_alerts', $themeName),
			'#description'    => t('If enabled, campus-wide alerts will be displayed at the top of the site.')
		];

		$form['ucb_breadcrumb_nav'] = [
			'#type'           => 'checkbox',
			'#title'          => t('Show breadcrumb navigation on pages'),
			'#default_value'  => theme_get_setting('ucb_breadcrumb_nav', $themeName),
			'#description'    => t('If enabled, the breadcrumb navigation will be shown at the top of pages, helping visitors find their way around the site.')
		];

		$form['ucb_gtm_account'] = [
			'#type'           => 'textfield',
			'#title'          => t('GTM Account Number'),
			'#default_value'  => theme_get_setting('ucb_gtm_account', $themeName),
			'#description'    => t('Google Tag Manager account number e.g. GTM-123456.'),
		];

		$form['ucb_secondary_menu_default_links'] = [
			'#type'           => 'checkbox',
			'#title'          => t('Display the standard Boulder secondary menu in the header navigation region.'),
			'#default_value'  => theme_get_setting('ucb_secondary_menu_default_links', $themeName),
			'#description'    => t('Check this box if you would like to display the default Boulder secondary menu links in the header.')
		];

		$form['ucb_secondary_menu_position'] = [
			'#type'           => 'select',
			'#title'          => t('Position of the secondary menu'),
			'#default_value'  => theme_get_setting('ucb_secondary_menu_position', $themeName),
			'#options'        => [
				'inline' => t('Inline with the main navigation'),
				'above'  => t('Above the main navigation')
			],
			'#description'    => t('The secondary menu of this site can be populated with secondary or action links and displayed inline with or above the main navigation.')
		];

		$form['ucb_secondary_menu_button_display'] = [
			'#type'           => 'checkbox',
			'#title'          => t('Display links in the secondary menu as buttions'),
			'#default_value'  => theme_get_setting('ucb_secondary_menu_button_display', $themeName),
			'#description'    => t('Check this box to display the links in the secondary menu of this site as buttons instead of links.')
		];

		$form['ucb_footer_menu_default_links'] = [
			'#type'           => 'checkbox',
			'#title'          => t('Display the standard Boulder menus in the footer region.'),
			'#default_value'  => theme_get_setting('ucb_footer_menu_default_links', $themeName),
			'#description'    => t('Check this box if you would like to display the default Boulder footer menu links in the footer.')
		];
		// Choose where social share buttons are positioned on each page
		$form['ucb_social_share_position'] = [
			'#type'           => 'select',
			'#title'          => t('Where your social media sharing links render'),
			'#default_value'  => theme_get_setting('ucb_social_share_position', $themeName),
			'#options'        => [
				t('None'),
				t('Left Side (Desktop) / Below Title (Mobile)'),
				t('Left Side (Desktop) / Below Content (Mobile)'),
				t('Below Content'),
				t('Below Title')
			],
			'#description'    => t('Select the location for social sharing links (Facebook, Twitter, etc) to appear on your pages.')
		];
		// Choose date/time format sitewide
		$form['ucb_date_format'] = [
			'#type'           => 'select',
			'#title'          => t('Display settings for Date formats on Articles'),
			'#default_value'  => theme_get_setting('ucb_date_format', $themeName),
			'#options'        => [
				t('Short Date'),
				t('Medium Date'),
				t('Long Date'),
				t('Short Date with Time'),
				t('Medium Date with Time'),
				t('Long Date with Time'),
				t('None - Hide')
			],
			'#description'    => t('Select the preferred Global Date/Time format for dates on your site.')
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
					'#title' => t('College ID'),
					// '#default_value' => $node->get('ucb_external_service_' . $externalServiceName . '__college_id')
				];
			break;
			default:
		}
	}

	/**
	 * @return array
	 *   The external services options available on content nodes.
	 */
	public function getContentExternalServicesOptions() {
		$externalServicesConfiguration = $this->configFactory->get('ucb_site_configuration.configuration')->get('external_services') ?? [];
		$externalServicesSettings = $this->configFactory->get('ucb_site_configuration.settings')->get('external_services') ?? [];
		$options = [];
		foreach ($externalServicesSettings as $externalServiceName => $externalServiceSettings) {
			if($externalServiceSettings['enabled'] === 'some')
				$options[$externalServiceName] = $externalServicesConfiguration[$externalServiceName]['content_label'];
		}
		return $options;
	}
}
