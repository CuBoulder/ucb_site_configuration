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
		$form = parent::form($form, $form_state);
		$allowedExternalServiceOptions = $this->service->getExternalServicesOptions();
		$exists = $this->exists($entity->id());
		$form['service_name'] = [
			'#type'  => 'radios',
			'#title' => $this->t('Service'),
			'#description' => $exists ? $this->t('To use a different service than the one selected, add it through the add page.') : '',
			'#options' => $allowedExternalServiceOptions,
			'#default_value' => $entity->getServiceName(),
			'#disabled' => $exists,
			'#required' => TRUE
		];
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
				$this->t('All pages of this site')
			],
			'#default_value' => $entity->isSitewide() ? 1 : 0,
			'#required' => TRUE
		];
		$form['node_entity_autocomplete'] = [
			'#type' => 'entity_autocomplete',
    		'#target_type' => 'node',
			'#title' => $this->t('Content'),
			'#description' => $this->t('Specify content to include this service on. Multiple entries may be seperated by commas.'),
			'#default_value' => $entity->getNodes(),
			'#tags' => TRUE,
			'#states' => [
				'visible' => [':input[name="sitewide"]' => ['value' => 0]]
			]
		];
		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(array $form, FormStateInterface $form_state) {
		/** @var \Drupal\ucb_site_configuration\Entity\ExternalServiceIncludeInterface */
		$entity = $this->entity;
		$entity->set('nodes', array_map(function($node){ return intval($node['target_id']); }, $form_state->getValue('node_entity_autocomplete')));
		\Drupal::logger('ucb_site_configuration')->notice(json_encode($entity->get('nodes')));
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
}
