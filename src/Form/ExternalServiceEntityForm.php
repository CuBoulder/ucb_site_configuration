<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Form\ExternalServiceEntityForm.
 */

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExternalServiceEntityForm extends EntityForm {

	/**
	 * The user site configuration service defined in this module.
	 *
	 * @var \Drupal\ucb_site_configuration\SiteConfiguration
	 */
	protected $service;

	/**
	 * Constructs an ExternalServiceEntityForm object.
	 * 
	 * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   	 *   The entity type manager.
	 *
	 * @param \Drupal\ucb_site_configuration\UserInviteHelperService $helper
	 *   The user invite helper service defined in this module.
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
		return 'ucb_external_service_entity_form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function form(array $form, FormStateInterface $form_state) {
		$form = parent::form($form, $form_state);
		$entity = $this->entity;
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
			  'exists' => [$this, 'exist'],
			],
			'#disabled' => !$entity->isNew()
		];
		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(array $form, FormStateInterface $form_state) {
		$entity = $this->entity;
		$status = $entity->save();

		if ($status === SAVED_NEW) {
			$this->messenger()->addMessage($this->t('The %label third-party service created.', [
				'%label' => $entity->label(),
			]));
		} else {
			$this->messenger()->addMessage($this->t('The %label third-party service updated.', [
				'%label' => $entity->label(),
			]));
		}

		$form_state->setRedirect('entity.ucb_external_service.collection');
	}

	/**
	 * Helper function to check whether the `ucb_external_service` configuration entity exists.
	 * 
	 * @see https://www.drupal.org/node/1809494
	 */
	public function exist($id) {
		$entity = $this->entityTypeManager->getStorage('ucb_external_service')->getQuery()
			->condition('id', $id)
			->execute();
		return (bool) $entity;
	}
}
