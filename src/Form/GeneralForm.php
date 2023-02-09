<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Form\GeneralForm.
 */

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GeneralForm extends ConfigFormBase {

	/**
	 * The user site configuration service defined in this module.
	 *
	 * @var \Drupal\ucb_site_configuration\SiteConfiguration
	 */
	protected $service;

	/**
	 * Constructs a GeneralForm object.
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
		$siteTypeOptions = $configuration->get('site_type_options');
		$siteAffiliationOptions = array_filter($configuration->get('site_affiliation_options'), function ($value) { return !$value['type_restricted']; });
		$form['site_name'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Site name'),
			'#default_value' => $this->config('system.site')->get('name'),
			'#required' => TRUE
		];
		$form['site_type'] = [
			'#type' => 'select',
			'#title' => $this->t('Site type'),
			'#default_value' => $settings->get('site_type'),
			'#options' => array_merge(['' => $this->t('- None -')], array_map(function ($value) { return $value['label']; }, $siteTypeOptions)),
			'#required' => FALSE
		];
		$affiliationHidesOn = [];
		foreach ($siteTypeOptions as $siteTypeId => $siteType) {
			if (isset($siteType['affiliation']))
				array_push($affiliationHidesOn, [':input[name="site_type"]' => ['value' => $siteTypeId]]);
		}
		$form['site_affiliation_container'] = [
			'#type' => 'container',
			'#states' => [
				'invisible' => $affiliationHidesOn
			],
			'site_affiliation' => [
				'#type' => 'select',
				'#title' => $this->t('Site affiliation'),
				'#default_value' => $settings->get('site_affiliation'),
				'#options' => array_merge(['' => $this->t('- None -')], array_map(function ($value) { return $value['label']; }, $siteAffiliationOptions), ['custom' =>  $this->t('Custom')]),
				'#required' => FALSE
			],
			'site_affiliation_custom' => [
				'#type' => 'fieldset',
				'#description' => $this->t('Define a title and optional URL for the custom site affiliation.'),
				'#states' => [
					'visible' => [[':input[name="site_affiliation"]' => ['value' => 'custom']]]
				],
				'site_affiliation_label' => [
					'#type' => 'textfield',
					'#title' => $this->t('Title'),
					'#default_value' => $settings->get('site_affiliation_label'),
					'#required' => FALSE,
					'#maxlength' => 255
				],
				'site_affiliation_url' => [
					'#type' => 'textfield',
					'#title' => $this->t('URL'),
					'#default_value' => $settings->get('site_affiliation_url'),
					'#required' => FALSE,
					'#maxlength' => 255
				]
			]
		];
		return parent::buildForm($form, $form_state);
	}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$configuration = $this->service->getConfiguration();
		$settings = $this->config('ucb_site_configuration.settings');
		$siteTypeOptions = $configuration->get('site_type_options');
		$this->config('system.site')->set('name', $form_state->getValue('site_name'))->save();
		$siteTypeId = $form_state->getValue('site_type');
		$siteAffiliationId = $form_state->getValue('site_affiliation');
		if ($siteTypeId && isset($siteTypeOptions[$siteTypeId]) && isset($siteTypeOptions[$siteTypeId]['affiliation']))
			$siteAffiliationId = $siteTypeOptions[$siteTypeId]['affiliation'];
		$this->config('ucb_site_configuration.settings')
			->set('site_type', $siteTypeId)
			->set('site_affiliation', $siteAffiliationId)
			->set('site_affiliation_label', $form_state->getValue('site_affiliation_label'))
			->set('site_affiliation_url', $form_state->getValue('site_affiliation_url'))
			->save();
		parent::submitForm($form, $form_state);
	}
}