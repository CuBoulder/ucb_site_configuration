<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Form\ExternalServiceIncludeEntityForm.
 */

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the form to add or edit an ExternalServiceInclude entity.
 */

class ExternalServiceIncludeEntityForm extends EntityForm {

	/**
	 * The user site configuration service defined in this module.
	 *
	 * @var \Drupal\ucb_site_configuration\SiteConfiguration
	 */
	protected $service;

	/**
	 * Constructs an ExternalServiceIncludeEntityForm object.
	 * 
	 * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   	 *   The entity type manager.
	 *
	 * @param \Drupal\ucb_site_configuration\SiteConfiguration $service
	 *   The service defined in this module.
	 */
	public function __construct(EntityTypeManagerInterface $entityTypeManager, SiteConfiguration $service) {
		$this->entityTypeManager = $entityTypeManager;
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
			$container->get('entity_type.manager'),
			$container->get('ucb_site_configuration')
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'ucb_external_service_include_entity_form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function form(array $form, FormStateInterface $form_state) {
		/** @var \Drupal\ucb_site_configuration\Entity\ExternalServiceIncludeInterface */
		$entity = $this->entity;
		$allowedExternalServiceOptions = $this->service->getExternalServicesOptions();
		$exists = $this->exists($entity->id());
		$form['service_name'] = [
			'#type'  => 'radios',
			'#title' => $this->t('Service'),
			'#options' => $allowedExternalServiceOptions,
			'#default_value' => $entity->getServiceName(),
			'#required' => TRUE
		];
		$externalServicesConfig = $this->service->getConfiguration()->get('external_services');
		foreach ($allowedExternalServiceOptions as $externalServiceName => $externalServiceLabel) {
			$serviceSettingsForm = [
				'#type' => 'details',
				'#title' => $this->t('%service configuration', ['%service' => $externalServiceLabel]),
				'#open' => !$exists || $externalServiceName != $entity->getServiceName(),
				'#states' => [
					'visible' => [
						':input[name="service_name"]' => ['value' => $externalServiceName]
					]
				]
			];
			$this->buildExternalServiceSiteSettingsForm($serviceSettingsForm, $externalServiceName, $externalServicesConfig[$externalServiceName], $entity->getServiceSettings(), $form_state);
			$form['service_' . $externalServiceName . '_settings'] = $serviceSettingsForm;
		}
		$form['label'] = [
			'#type' => 'textfield',
			'#title' => t('Label'),
			'#maxlength' => 255,
			'#default_value' => $entity->label(),
			'#required' => TRUE
		];
		$form['id'] = [
			'#type' => 'machine_name',
			'#default_value' => $entity->id(),
			'#machine_name' => [
			  'exists' => [$this, 'exists'],
			],
			'#disabled' => !$entity->isNew()
		];
		$form['sitewide'] = [
			'#type'  => 'radios',
			'#title' => t('Include this service on'),
			'#options' => [
				$this->t('Specific content'),
				$this->t('All content on this site')
			],
			'#default_value' => $entity->isSitewide() ? 1 : 0,
			'#required' => TRUE
		];
		$form['content_options_container'] = [
			'#type' => 'container',
			'node_entity_autocomplete' => [
				'#type' => 'entity_autocomplete',
				'#target_type' => 'node',
				'#title' => $this->t('Content'),
				'#description' => $this->t('Specify content to include this service on. Multiple entries may be seperated by commas.'),
				'#default_value' => $entity->getNodes(),
				'#tags' => TRUE,
			],
			'content_editing_enabled' => [
				'#type' => 'checkbox',
				'#target_type' => 'node',
				'#title' => $this->t('Allow content authors to add or remove this service for content they can edit'),
				'#description' => $this->t('If enabled, all users will be able to add or remove this service for content they can edit, including when creating new content. A user with permission to administer third-party services will always be able to add or remove this service, regardless if enabled.'),
				'#default_value' => $entity->isContentEditingEnabled(),
				'#tags' => TRUE,
				'#states' => [
					'visible' => [':input[name="sitewide"]' => ['value' => 0]]
				]
			],
			'#states' => [
				'visible' => [':input[name="sitewide"]' => ['value' => 0]]
			]
		];
		return $form + parent::form($form, $form_state);
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateForm(array &$form, FormStateInterface $form_state) { // TODO: Finish validation function
		$externalServiceName = $form_state->getValue('service_name');
		switch ($externalServiceName) {
			case 'mainstay':
				$licenseIdFieldName = $externalServiceName . '__bot_token';
				$licenseId = $form_state->getValue($licenseIdFieldName);
				if(!preg_match('/^[a-z0-9]+$/', $licenseId))
					$form_state->setErrorByName($licenseIdFieldName, $this->t('A valid bot token is a mix of lowercase letters and numbers.'));
			break;
			case 'livechat':
				$licenseIdFieldName = $externalServiceName . '__license_id';
				$licenseId = $form_state->getValue($licenseIdFieldName);
				if(!preg_match('/^[0-9]+$/', $licenseId))
					$form_state->setErrorByName($licenseIdFieldName, $this->t('A valid license ID contains only numbers.'));
			break;
			case 'statuspage':
				$pageIdFieldName = $externalServiceName . '__page_id';
				$pageId = $form_state->getValue($pageIdFieldName);
				if(strlen($pageId) != 12 || !preg_match('/^[a-z0-9]+$/', $pageId)) // preg_match validation on page id is important and stops potential script injection
					$form_state->setErrorByName($pageIdFieldName, $this->t('A valid page ID is 12 characters long and a mix of lowercase letters and numbers.'));
			break;
			default:
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(array $form, FormStateInterface $form_state) {
		/** @var \Drupal\ucb_site_configuration\Entity\ExternalServiceIncludeInterface */
		$entity = $this->entity;
		// Set `service_settings` for the selected service
		$externalServiceName = $entity->getServiceName();
		$externalServiceConfig = $this->service->getConfiguration()->get('external_services')[$externalServiceName];
		$externalServiceSettings = [];
		foreach ($externalServiceConfig['settings'] as $settingName)
			$externalServiceSettings[$settingName] = $form_state->getValue($externalServiceName . '__' . $settingName) ?? '';
		$entity->set('service_settings', $externalServiceSettings);
		// Set `nodes` from the special content field
		$entity->set('nodes', array_map(function($node){ return intval($node['target_id']); }, $form_state->getValue('node_entity_autocomplete') ?? []));
		// Save the entity
		$status = $entity->save();
		if ($status === SAVED_NEW) {
			$this->messenger()->addMessage($this->t('The %label third-party service created.', ['%label' => $entity->label()]));
		} else {
			$this->messenger()->addMessage($this->t('The %label third-party service updated.', ['%label' => $entity->label()]));
		}
		$form_state->setRedirect('entity.ucb_external_service_include.collection');
	}

	/**
	 * Helper function to check whether the ExternalServiceInclude configuration entity exists.
	 * 
	 * @see https://www.drupal.org/node/1809494
	 */
	public function exists($id) {
		$entity = $this->entityTypeManager->getStorage('ucb_external_service_include')->getQuery()
			->condition('id', $id)
			->execute();
		return (bool) $entity;
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
				$form[$externalServiceName . '__bot_token'] = [
					'#type' => 'textfield',
					'#size' => 24,
					'#maxlength' => 24,
					'#title' => $this->t('Bot token'),
					'#default_value' => $externalServiceSettings['bot_token'],
					'#states' => [
						'required' => [
							':input[name="service_name"]' => ['value' => $externalServiceName]
						]
					]
				];
				$form[$externalServiceName . '__college_id'] = [
					'#type' => 'textfield',
					'#size' => 64,
					'#maxlength' => 64,
					'#title' => $this->t('College ID'),
					'#default_value' => $externalServiceSettings['college_id'],
					'#states' => [
						'required' => [
							':input[name="service_name"]' => ['value' => $externalServiceName]
						]
					]
				];
			break;
			case 'livechat':
				$form[$externalServiceName . '__license_id'] = [
					'#type' => 'textfield',
					'#size' => 12,
					'#maxlength' => 12,
					'#title' => $this->t('License ID'),
					'#default_value' => $externalServiceSettings['license_id'],
					'#states' => [
						'required' => [
							':input[name="service_name"]' => ['value' => $externalServiceName]
						]
					]
				];
			break;
			case 'statuspage':
				$form[$externalServiceName . '__page_id'] = [ // TODO: validate page id as a letter/number string, exactly 12 characters
					'#type' => 'textfield',
					'#size' => 12,
					'#maxlength' => 12,
					'#title' => $this->t('Page ID'),
					'#default_value' => $externalServiceSettings['page_id'],
					'#states' => [
						'required' => [
							':input[name="service_name"]' => ['value' => $externalServiceName]
						]
					]
				];
			break;
			default:
		}
	}
}
