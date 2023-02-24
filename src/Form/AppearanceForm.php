<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Form\AppearanceForm.
 */

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\ThemeSettingsForm;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

class AppearanceForm extends ThemeSettingsForm {

	/**
	 * The user site configuration service defined in this module.
	 *
	 * @var \Drupal\ucb_site_configuration\SiteConfiguration
	 */
	protected $service;

	/**
	 * Constructs an AppearanceForm object.
	 *
	 * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
	 *   The factory for configuration objects.
	 * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
	 *   The module handler instance to use.
	 * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
	 *   The theme handler.
	 * @param \Symfony\Component\Mime\MimeTypeGuesserInterface $mime_type_guesser
	 *   The MIME type guesser instance to use.
	 * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
	 *   The theme manager.
	 * @param \Drupal\Core\File\FileSystemInterface $file_system
	 *   The file system.
	 * @param \Drupal\ucb_site_configuration\SiteConfiguration $service
	 *   The service defined in this module.
	 */
	public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, ThemeHandlerInterface $theme_handler, MimeTypeGuesserInterface $mime_type_guesser, ThemeManagerInterface $theme_manager, FileSystemInterface $file_system, SiteConfiguration $service) {
		parent::__construct($config_factory, $module_handler, $theme_handler, $mime_type_guesser, $theme_manager, $file_system);
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
			$container->get('module_handler'),
			$container->get('theme_handler'),
			$container->get('file.mime_type.guesser'),
			$container->get('theme.manager'),
			$container->get('file_system'),
			$container->get('ucb_site_configuration')
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'ucb_site_configuration_appearance_form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(array $form, FormStateInterface $form_state, $theme = '') {
		$theme = $this->service->getThemeName();
		$form = parent::buildForm($form, $form_state, $theme);
		unset($form['theme_settings'], $form['logo'], $form['favicon']); // Removes some defaults we don't care about
		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm(array &$form, FormStateInterface $form_state) {
		// $config = $this->config($this->service->getThemeName() . '.settings');
		// $values = $form_state->getValues();
		parent::submitForm($form, $form_state);
	}
}
