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
use Drupal\Core\Session\AccountInterface;
use Drupal\file\Entity\File;

class AppearanceForm extends ThemeSettingsForm {

	/**
	 * The current user.
	 *
	 * @var \Drupal\Core\Session\AccountInterface
	 */
	protected $user;

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
	 * @param \Drupal\Core\Session\AccountInterface $user
	 *   The current user.
	 * @param \Drupal\ucb_site_configuration\SiteConfiguration $service
	 *   The service defined in this module.
	 */
	public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, ThemeHandlerInterface $theme_handler, MimeTypeGuesserInterface $mime_type_guesser, ThemeManagerInterface $theme_manager, FileSystemInterface $file_system, AccountInterface $user, SiteConfiguration $service) {
		parent::__construct($config_factory, $module_handler, $theme_handler, $mime_type_guesser, $theme_manager, $file_system);
		$this->user = $user;
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
			$container->get('current_user'),
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
		if ($this->user->hasPermission('edit ucb site advanced')) {
			$advanced = [];
			$advanced['custom_logo'] = [
				'#type' => 'container'
			];
			$advanced['custom_logo']['ucb_use_custom_logo'] = [
				'#type' => 'checkbox',
				'#title' => $this->t('Use a custom logo image'),
				'#default_value' => theme_get_setting('ucb_use_custom_logo', $theme),
				'#tree' => FALSE
			];
			$advanced['custom_logo']['settings'] = [
				'#type' => 'container',
				'#states' => [
					'visible' => [
						'input[name="ucb_use_custom_logo"]' => ['checked' => TRUE]
					]
				]
			];
			$advanced['custom_logo']['settings']['ucb_custom_logo_scale'] = [
				'#type'  => 'select',
				'#title' => $this->t('Custom logo scale'),
				'#description' => $this->t('Define the scale of the custom logo image. E.g. if 2x is selected, upload an image that is <em>exactly twice</em> the normal size of the logo (meaning for a logo meant to display at 200x36, the image you will upload is 400x72). Setting this to a higher value ensures the logo always remains sharp on high-resolution devices.'),
				'#options' => [
					'1x' => $this->t('1x'),
					'2x' => $this->t('2x'),
					'3x' => $this->t('3x')
				],
				'#default_value' => theme_get_setting('ucb_custom_logo_scale', $theme) ?? '2x'
			];
			$advanced['custom_logo']['settings']['dark'] = [
				'#type' => 'details',
				'#title' => $this->t('Custom logo – White text on dark header'),
				'#description' => $this->t('Provide a logo to use with a dark header. Text in the logo should be legible on dark backgrounds.'),
				'#open' => TRUE
			];
			$advanced['custom_logo']['settings']['light'] = [
				'#type' => 'details',
				'#title' => $this->t('Custom logo – Black text on light header'),
				'#description' => $this->t('Provide a logo to use with a light header. Text in the logo should be legible on light backgrounds.'),
				'#open' => TRUE
			];
			$advanced['custom_logo']['settings']['dark']['ucb_custom_logo_dark_path'] = [
				'#type' => 'textfield',
				'#title' => $this->t('Path to a custom logo'),
				'#default_value' => theme_get_setting('ucb_custom_logo_dark_path', $theme)
			];
			$advanced['custom_logo']['settings']['dark']['ucb_custom_logo_dark_upload'] = [
				'#type' => 'managed_file',
				'#title' => $this->t('Upload a custom logo'),
				'#description' => $this->t('You may upload a custom logo to use here. The path will be set to the uploaded file automatically when the form is submitted.'),
				'#multiple' => FALSE,
				'#upload_location' => 'public://custom_logo/',
				'#upload_validators' => [
					'file_validate_is_image' => []
				]
			];
			$advanced['custom_logo']['settings']['light']['ucb_custom_logo_light_path'] = [
				'#type' => 'textfield',
				'#title' => $this->t('Path to a custom logo'),
				'#default_value' => theme_get_setting('ucb_custom_logo_light_path', $theme)
			];
			$advanced['custom_logo']['settings']['light']['ucb_custom_logo_light_upload'] = [
				'#type' => 'managed_file',
				'#title' => $this->t('Upload a custom logo'),
				'#description' => $this->t('You may upload a custom logo to use here. The path will be set to the uploaded file automatically when the form is submitted.'),
				'#multiple' => FALSE,
				'#upload_location' => 'public://custom_logo/',
				'#upload_validators' => [
					'file_validate_is_image' => []
				]
			];
			$form['advanced'] = array_merge($advanced, $form['advanced']);
		}
		unset($form['theme_settings'], $form['logo'], $form['favicon']); // Removes some defaults that are normally visible at the top of the theme settings
		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$fidsDark = $form_state->getValue('ucb_custom_logo_dark_upload');
		if ($fidsDark && isset($fidsDark[0]) && ($file = File::load($fidsDark[0]))) {
			$file->setPermanent();
			$file->save();
			$form_state->setValue('ucb_custom_logo_dark_path', $file->getFileUri());
		}
		$fidsLight = $form_state->getValue('ucb_custom_logo_light_upload');
		if ($fidsLight && isset($fidsLight[0]) && ($file = File::load($fidsLight[0]))) {
			$file->setPermanent();
			$file->save();
			$form_state->setValue('ucb_custom_logo_light_path', $file->getFileUri());
		}
		$form_state->unsetValue('ucb_custom_logo_dark_upload');
		$form_state->unsetValue('ucb_custom_logo_light_upload');
		parent::submitForm($form, $form_state);
	}
}
