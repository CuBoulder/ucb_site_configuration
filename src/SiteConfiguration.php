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

		$form['ucb_secondary_menu'] = [
			'#type'           => 'checkbox',
			'#title'          => t('Display the standard Boulder secondary menu in the header navigation region.'),
			'#default_value'  => theme_get_setting('ucb_secondary_menu', $themeName),
			'#description'    => t('Check this box if you would like to display the default Boulder secondary menu links in the header.')
		];

		$form['ucb_footer_menu'] = [
			'#type'           => 'checkbox',
			'#title'          => t('Display the standard Boulder menus in the header footer region.'),
			'#default_value'  => theme_get_setting('ucb_footer_menu', $themeName),
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
	}
}
