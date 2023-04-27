<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Form\RelatedArticlesForm.
 */

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RelatedArticlesForm extends ConfigFormBase {

	/**
	 * The entity type manager.
	 *
	 * @var \Drupal\Core\Entity\EntityTypeManagerInterface
	 */
	protected $entityTypeManager;

	/**
	 * The user site configuration service defined in this module.
	 *
	 * @var \Drupal\ucb_site_configuration\SiteConfiguration
	 */
	protected $service;

	/**
	 * Constructs a RelatedArticlesForm object.
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
		return 'ucb_site_configuration_related_articles_form';
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
	public function buildForm(array $form, FormStateInterface $form_state) {
		$settings = $this->service->getSettings();
		$entityStorage = $this->entityTypeManager->getStorage('taxonomy_term');
		$categoryTerms = $entityStorage->loadByProperties(['vid' => 'category']);
		$categoryOptions = [];
		$tagTerms = $entityStorage->loadByProperties(['vid' => 'tags']);
		$tagOptions = [];
		foreach ($categoryTerms as $categoryTerm)
			$categoryOptions[$categoryTerm->id()] = $categoryTerm->label();
		foreach ($tagTerms as $tagTerm)
			$tagOptions[$tagTerm->id()] = $tagTerm->label();
		$form['exclude_categories'] = [
			'#type' => 'checkboxes',
			'#title' => $this->t('Exclude categories'),
			'#default_value' => $settings->get('related_articles_exclude_categories') ?? [],
			'#options' => $categoryOptions,
			'#required' => FALSE
		];
		$form['exclude_tags'] = [
			'#type' => 'checkboxes',
			'#title' => $this->t('Exclude tags'),
			'#default_value' => $settings->get('related_articles_exclude_tags') ?? [],
			'#options' => $tagOptions,
			'#required' => FALSE
		];
		return parent::buildForm($form, $form_state);
	}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$formValues = $form_state->getValues();
		$this->config('ucb_site_configuration.settings')
			->set('related_articles_exclude_categories', $form_state->getValue('exclude_categories'))
			->set('related_articles_exclude_tags', $form_state->getValue('exclude_tags'))
			->save();
		parent::submitForm($form, $form_state);
	}
}
