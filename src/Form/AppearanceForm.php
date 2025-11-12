<?php

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The form for the "Appearance" tab in CU Boulder site settings.
 */
class AppearanceForm extends ConfigFormBase {
  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

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
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   * @param \Drupal\ucb_site_configuration\SiteConfiguration $service
   *   The site configuration service defined in this module.
   */
  public function __construct(ConfigFactoryInterface $config_factory, TypedConfigManagerInterface $typed_config_manager, FileSystemInterface $file_system, AccountInterface $user, SiteConfiguration $service) {
    parent::__construct($config_factory, $typed_config_manager);
    $this->fileSystem = $file_system;
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
      $container->get('config.typed'),
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
  protected function getEditableConfigNames() {
    return [$this->service->getThemeName() . '.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $theme = '') {
    $theme = $this->service->getThemeName();
    $form = parent::buildForm($form, $form_state, $theme);
    $this->service->buildThemeSettingsForm($form, $form_state);
    if ($this->user->hasPermission('edit ucb site advanced')) {
      $advanced = [];
      $advanced['ucb_sidebar_position'] = [
        '#type' => 'select',
        '#title' => $this->t('Sidebar position'),
        '#default_value' => theme_get_setting('ucb_sidebar_position', $theme),
        '#options' => [
          'right' => $this->t('Right (default)'),
          'left' => $this->t('Left'),
        ],
        '#description' => $this->t('Select if sidebar content should appear on the left or right side of a page.'),
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

    // Excludes unnecessary elements before saving.
    $form_state->cleanValues();
    $form_state->unsetValue('ucb_custom_logo_dark_upload');
    $form_state->unsetValue('ucb_custom_logo_light_upload');
  
    $values = $form_state->getValues();
    $config = $this->config($this->service->getThemeName() . '.settings');
    theme_settings_convert_to_config($values, $config)->save();

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

  /**
   * Helper function for the system_theme_settings form.
   *
   * Attempts to validate normal system paths, paths relative to the public files
   * directory, or stream wrapper URIs. If the given path is any of the above,
   * returns a valid path or URI that the theme system can display.
   *
   * @param string $path
   *   A path relative to the Drupal root or to the public files directory, or
   *   a stream wrapper URI.
   *
   * @return mixed
   *   A valid path that can be displayed through the theme system, or FALSE if
   *   the path could not be validated.
   * 
   * @see \Drupal\system\Form\ThemeSettingsForm->validatePath
   */
  protected function validatePath($path) {
    // Absolute local file paths are invalid.
    if ($this->fileSystem->realpath($path) == $path) {
      return FALSE;
    }
    // A path relative to the Drupal root or a fully qualified URI is valid.
    if (is_file($path)) {
      return $path;
    }
    // Prepend 'public://' for relative file paths within public filesystem.
    if (StreamWrapperManager::getScheme($path) === FALSE) {
      $path = 'public://' . $path;
    }
    if (is_file($path)) {
      return $path;
    }
    return FALSE;
  }

}
