<?php

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

/**
 * Builds the form to delete an ExternalService entity.
 */

class ExternalServiceEntityDeleteForm extends EntityConfirmFormBase {
	/**
	 * {@inheritdoc}
	 */
	public function getQuestion() {
		return $this->t('Are you sure you want to delete the third-party service %name?', ['%name' => $this->entity->label()]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCancelUrl() {
		return Url::fromRoute('entity.ucb_external_service.collection');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfirmText() {
		return $this->t('Delete');
	}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$this->entity->delete();
		$this->messenger()->addMessage($this->t('Third-party service %label has been deleted.', ['%label' => $this->entity->label()]));
		$form_state->setRedirectUrl($this->getCancelUrl());
	}
}
