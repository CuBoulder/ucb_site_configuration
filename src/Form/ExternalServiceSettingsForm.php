<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Form\ExternalServiceSettingsForm.
 */

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExternalServiceSettingsForm extends ConfigFormBase {

	/**
	 * The user site configuration service defined in this module.
	 *
	 * @var \Drupal\ucb_site_configuration\SiteConfiguration
	 */
	protected $service;

	/**
	 * Constructs a ExternalServiceSettingsForm object.
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
		return ['ucb_site_configuration.settings'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'ucb_site_configuration_external_service_settings_form';
	}

	/**
	 * Builds the "Third-party services" form.
	 * 
	 * {@inheritdoc}
	 */
	public function buildForm(array $form, FormStateInterface $form_state) {
		$externalServicesConfig = $this->config('ucb_site_configuration.configuration')->get('external_services');
		$externalServicesSettings = $this->config('ucb_site_configuration.settings')->get('external_services');
		foreach ($externalServicesConfig as $externalServiceName => $externalServiceConfig) {
			$externalServiceSettings = $externalServicesSettings[$externalServiceName];
			$enabledOptions = ['0' => $this->t('Disabled')];
			if(isset($externalServiceConfig['availability'])) {
				if ($externalServiceConfig['availability']['content'])
					$enabledOptions['some'] = $this->t('Enabled for adding to individual content pages');
				if ($externalServiceConfig['availability']['sitewide'])
					$enabledOptions['all'] = $this->t('Enabled on the entire site');
			}
			$form['service_' . $externalServiceName] = [
				'#type' => 'details',
				'#title' => $externalServiceConfig['label'],
				'#open' => TRUE,
				'service_' . $externalServiceName . '_enabled' => [
					'#type' => 'radios',
					'#options' => $enabledOptions,
					'#default_value' => $externalServiceSettings['enabled']
				]
			];
			$externalServiceCustomSettingsForm = [];
			$this->buildExternalServiceSiteSettingsForm($externalServiceCustomSettingsForm, $externalServiceName, $externalServiceConfig, $externalServiceSettings, $form_state);
			if ($externalServiceCustomSettingsForm) {
				$externalServiceCustomSettingsForm['#type'] = 'fieldset';
				$externalServiceCustomSettingsForm['#states'] = [
					'invisible' => [
						':input[name="service_' . $externalServiceName .'_enabled"]' => ['value' => '0']
				  	]
				];
				$form['service_' . $externalServiceName]['service_settings_' . $externalServiceName] = $externalServiceCustomSettingsForm;
			}
		}
		return parent::buildForm($form, $form_state);
	}

	/**
	 * Builds the inner settings form for an external service on the "Third-party services" administration page.
	 * 
	 * @param array &$form
	 *   The form build array.
	 * @param string $externalServiceName
	 *   The machine name of the external srvice.
	 * @param array $externalServiceConfig
	 *   The fixed configuration of the external service.
	 * @param array $externalServiceSettings
	 *   The editable settings of the external service.
	 * @param FormStateInterface $form_state
	 *   The current state of the form.
	 */
	private function buildExternalServiceSiteSettingsForm(array &$form, $externalServiceName, $externalServiceConfig, $externalServiceSettings, FormStateInterface $form_state) {
		switch ($externalServiceName) {
			case 'mainstay':
				$form['service_' . $externalServiceName . '__license_id'] = [
					'#type' => 'textfield',
					'#size' => '60',
					'#title' => t('License ID'),
					'#default_value' => $externalServiceSettings['_license_id'],
					'#states' => [
						'optional' => [
							':input[name="service_' . $externalServiceName . '_enabled"]' => ['value' => '0']
						]
					]
				];
				$form['service_' . $externalServiceName . '__college_id'] = [
					'#type' => 'textfield',
					'#size' => '60',
					'#title' => t('College ID'),
					'#description' => t('If enabled for adding to individual content pages, the College ID becomes editable for each page and can be left blank here.'),
					'#default_value' => $externalServiceSettings['_college_id']
				];
			break;
			case 'livechat':
				$form['service_' . $externalServiceName . '__license_id'] = [
					'#type' => 'textfield',
					'#size' => '60',
					'#title' => t('License ID'),
					'#default_value' => $externalServiceSettings['_license_id'],
					'#states' => [
						'optional' => [
							':input[name="service_' . $externalServiceName . '_enabled"]' => ['value' => '0']
						]
					]
				];
			break;
			default:
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$settings = $this->config('ucb_site_configuration.settings');
		$newExternalServicesSettings = [];
		foreach ($settings->get('external_services') as $externalServiceName => $externalServiceSettings) {
			$newExternalServiceSettings = [];
			foreach ($externalServiceSettings as $settingName => $settingValue)
				$newExternalServiceSettings[$settingName] = $form_state->getValue('service_' . $externalServiceName . '_' . $settingName);
			$newExternalServicesSettings[$externalServiceName] = $newExternalServiceSettings;
		}
		$settings->set('external_services', $newExternalServicesSettings);
		$settings->save();
		parent::submitForm($form, $form_state);
	}
}
