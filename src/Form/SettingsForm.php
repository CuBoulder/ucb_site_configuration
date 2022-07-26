<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Form\SettingsForm.
 */

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SettingsForm extends ConfigFormBase {

	/**
	 * The user site configuration service defined in this module.
	 *
	 * @var \Drupal\ucb_site_configuration\SiteConfiguration
	 */
	protected $service;

	/**
	 * Constructs a SettingsForm object.
	 *
	 * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
	 *   The config factory.
	 * @param \Drupal\ucb_site_configuration\UserInviteHelperService $helper
	 *   The user invite helper service defined in this module.
	 */
	public function __construct(ConfigFactoryInterface $config_factory, SiteConfiguration $service) {
		parent::__construct($config_factory);
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
			$container->get('ucb_site_configuration')
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getEditableConfigNames() {
		return [$this->service->getThemeName() . '.settings', 'ucb_site_configuration.settings'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'ucb_site_configuration_settings_form';
	}

	/**
	 * @return string[]
	 *   A list of theme settings which are editable for users with the `administer ucb site` permission
	 *   required to access this form. Configurable in `config/install/ucb_site_configuration.configuration.yml`
	 *   at the root of this module.
	 */
	protected function getEditableThemeSettings() {
		return $this->config('ucb_site_configuration.configuration')->get('editable_theme_settings');
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(array $form, FormStateInterface $form_state) {
		$themeSettings = [];
		$themeForm = [
			'#type' => 'details',
			'#title' => 'Appearance',
			'#open' => TRUE
		];
		$editableThemeSettings = $this->getEditableThemeSettings();
		$this->service->buildThemeSettingsForm($themeSettings, $form_state);
		foreach($themeSettings as $themeSettingName => $themeSettingValue) {
			if(in_array($themeSettingName, $editableThemeSettings)) {
				$themeForm[$themeSettingName] = $themeSettingValue;
			}
		}
		$form['theme_settings'] = $themeForm;
		return parent::buildForm($form, $form_state);
	}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$config = $this->config($this->service->getThemeName() . '.settings');
		$editableThemeSettings = $this->getEditableThemeSettings();
		foreach($editableThemeSettings as $themeSettingName) {
			$config->set($themeSettingName, $form_state->getValue($themeSettingName));
		}
		$config->save();
		parent::submitForm($form, $form_state);
	}
}
