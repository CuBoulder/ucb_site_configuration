<?php

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\system\Form\ThemeSettingsForm;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

/**
 * The form for the "Appearance" tab in CU Boulder site settings.
 */
class AppearanceForm extends ThemeSettingsForm {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * The site configuration service defined in this module.
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
   *   The site configuration service defined in this module.
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
      $advanced['ucb_rave_alerts'] = [
        '#type'           => 'checkbox',
        '#title'          => $this->t('Show campus-wide alerts'),
        '#default_value'  => theme_get_setting('ucb_rave_alerts', $theme),
        '#description'    => $this->t('If enabled, campus-wide alerts will be displayed at the top of the site.'),
      ];
      $advanced['custom_logo'] = [
        '#type' => 'container',
      ];
      $advanced['custom_logo']['ucb_use_custom_logo'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Use a custom logo image'),
        '#default_value' => theme_get_setting('ucb_use_custom_logo', $theme),
        '#tree' => FALSE,
      ];
      $advanced['custom_logo']['settings'] = [
        '#type' => 'container',
        '#states' => [
          'visible' => [
            'input[name="ucb_use_custom_logo"]' => ['checked' => TRUE],
          ],
        ],
      ];
      $advanced['custom_logo']['settings']['ucb_custom_logo_scale'] = [
        '#markup' => $this->t('For a custom logo, use an image that is <em>exactly twice</em> the normal size of the logo (meaning for a logo meant to display at 200x36, the image you will upload is 400x72).'),
      ];
      $advanced['custom_logo']['settings']['dark'] = [
        '#type' => 'details',
        '#title' => $this->t('Custom logo – White text on dark header'),
        '#description' => $this->t('Provide a logo to use with a dark header. Text in the logo should be legible on dark backgrounds.'),
        '#open' => TRUE,
      ];
      $advanced['custom_logo']['settings']['light'] = [
        '#type' => 'details',
        '#title' => $this->t('Custom logo – Black text on light header'),
        '#description' => $this->t('Provide a logo to use with a light header. Text in the logo should be legible on light backgrounds.'),
        '#open' => TRUE,
      ];
      $advanced['custom_logo']['settings']['dark']['ucb_custom_logo_dark_path'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Path to a custom logo'),
        '#default_value' => theme_get_setting('ucb_custom_logo_dark_path', $theme),
      ];
      $advanced['custom_logo']['settings']['dark']['ucb_custom_logo_dark_upload'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Upload a custom logo'),
        '#description' => $this->t("You may upload a custom logo image to use here (files larger than 2 MB won't be accepted). The path will be set to the uploaded file automatically when the form is submitted."),
        '#multiple' => FALSE,
        '#upload_location' => 'public://custom_logo/',
        '#upload_validators' => [
          'file_validate_is_image' => [],
          // 2 * 1024 * 1024 = 2097152
          'file_validate_size' => [2097152],
        ],
      ];
      $advanced['custom_logo']['settings']['light']['ucb_custom_logo_light_path'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Path to a custom logo'),
        '#default_value' => theme_get_setting('ucb_custom_logo_light_path', $theme),
      ];
      $advanced['custom_logo']['settings']['light']['ucb_custom_logo_light_upload'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Upload a custom logo'),
        '#description' => $this->t("You may upload a custom logo image to use here (files larger than 2 MB won't be accepted). The path will be set to the uploaded file automatically when the form is submitted."),
        '#multiple' => FALSE,
        '#upload_location' => 'public://custom_logo/',
        '#upload_validators' => [
          'file_validate_is_image' => [],
          // 2 * 1024 * 1024 = 2097152
          'file_validate_size' => [2097152],
        ],
      ];
      $form['advanced'] = array_merge($advanced, $form['advanced']);
    }
    // Removes some defaults that are normally visible at the top of the theme
    // settings.
    unset($form['theme_settings'], $form['logo'], $form['favicon']);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    // Validates the custom logo path fields, setting path equal to an uploaded
    // file path (if applicable).
    if ($form_state->getValue('ucb_use_custom_logo')) {
      $fidsDark = $form_state->getValue('ucb_custom_logo_dark_upload');
      if ($fidsDark && isset($fidsDark[0]) && ($file = File::load($fidsDark[0]))) {
        $form_state->setValue('ucb_custom_logo_dark_path', $file->getFileUri());
      }
      $fidsLight = $form_state->getValue('ucb_custom_logo_light_upload');
      if ($fidsLight && isset($fidsLight[0]) && ($file = File::load($fidsLight[0]))) {
        $form_state->setValue('ucb_custom_logo_light_path', $file->getFileUri());
      }
      $pathDark = $this->validatePath($form_state->getValue('ucb_custom_logo_dark_path'));
      if ($pathDark) {
        $form_state->setValue('ucb_custom_logo_dark_path', $pathDark);
      }
      else {
        $form_state->setErrorByName('ucb_custom_logo_dark_path', $this->t('The white text on dark header custom logo path is invalid. Please either upload an image or specify a valid file path to use a custom logo.'));
      }
      $pathLight = $this->validatePath($form_state->getValue('ucb_custom_logo_light_path'));
      if ($pathLight) {
        $form_state->setValue('ucb_custom_logo_light_path', $pathLight);
      }
      else {
        $form_state->setErrorByName('ucb_custom_logo_light_path', $this->t('The black text on light header custom logo path is invalid. Please either upload an image or specify a valid file path to use a custom logo.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fidsDark = $form_state->getValue('ucb_custom_logo_dark_upload');
    if ($fidsDark && isset($fidsDark[0]) && ($file = File::load($fidsDark[0]))) {
      $this->makePermanent($file);
    }
    $fidsLight = $form_state->getValue('ucb_custom_logo_light_upload');
    if ($fidsLight && isset($fidsLight[0]) && ($file = File::load($fidsLight[0]))) {
      $this->makePermanent($file);
    }
    $form_state->unsetValue('ucb_custom_logo_dark_upload');
    $form_state->unsetValue('ucb_custom_logo_light_upload');

    parent::submitForm($form, $form_state);
  }

  /**
   * This helper function makes a file permanent.
   *
   * @param \Drupal\file\FileInterface $file
   *   A file entity.
   */
  protected function makePermanent(FileInterface $file) {
    $file->setPermanent();
    $file->save();
  }

}
