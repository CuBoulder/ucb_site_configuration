<?php

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * The form for the "Contact info" tab in CU Boulder site settings.
 */
class ContactInfoForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ucb_site_configuration_contact_info_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ucb_site_configuration.contact_info'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ucb_site_configuration.contact_info');
    // Toggle for icons.
    $generalStoredValues = $config->get('general');
    $emailStoredValues = $config->get('email');
    $phoneStoredValues = $config->get('phone');
    if ($generalStoredValues) {
      $this->buildFormSection(count($generalStoredValues), 'Contact information', 'contact information', 'general', 'Label (optional)', 'Value', 'text_format', 255, $generalStoredValues, $form);
    }
    if ($emailStoredValues) {
      $this->buildFormSection(count($emailStoredValues), 'Email address', 'email address', 'email', 'Label (optional)', 'Value', 'email', 20, $emailStoredValues, $form);
    }
    if ($phoneStoredValues) {
      $this->buildFormSection(count($phoneStoredValues), 'Phone number', 'phone number', 'phone', 'Label (optional)', 'Value', 'tel', 20, $phoneStoredValues, $form);
    }
    $form['icons_visible'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show email and / or phone icons in the site footer'),
      '#default_value' => $config->get('icons_visible') ?? TRUE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Builds a section for general, email, or phone number.
   */
  private function buildFormSection($itemCount, $verboseName, $verboseNameLower, $machineName, $labelFieldLabel, $valueFieldLabel, $valueFieldType, $valueFieldSize, $storedValues, array &$form) {
    // Toggle for "Add primary x".
    $form[$machineName . '_0_visible'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add primary ' . $verboseNameLower),
      '#default_value' => $storedValues[0]['visible'] ?? FALSE,
    ];
    // Section "Primary x".
    $sectionForm = [
      '#type' => 'details',
      '#title' => $this->t('Primary ' . $verboseNameLower),
      '#open' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="' . $machineName . '_0_visible"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // Fields for primary item.
    $this->buildFieldSection(0, $verboseName, $verboseNameLower, $machineName, $labelFieldLabel, $valueFieldLabel, $valueFieldType, $valueFieldSize, $storedValues, $sectionForm);
    // Add secondary items.
    for ($index = 1; $index < $itemCount; $index++) {
      // Toggle for "Add another x".
      $sectionForm[$machineName . '_' . $index . '_visible'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Add another ' . $verboseNameLower),
        '#default_value' => $storedValues[$index]['visible'] ?? FALSE,
      ];
      // Section "[another] x".
      $subSectionForm = [
        '#type' => 'details',
        '#title' => $this->t($verboseName),
        '#open' => TRUE,
        '#states' => [
          'visible' => [
            ':input[name="' . $machineName . '_' . $index . '_visible"]' => ['checked' => TRUE],
          ],
        ],
      ];
      // Fields for secondary item.
      $this->buildFieldSection($index, $verboseName, $verboseNameLower, $machineName, $labelFieldLabel, $valueFieldLabel, $valueFieldType, $valueFieldSize, $storedValues, $subSectionForm);
      $sectionForm[$machineName . '_' . $index] = $subSectionForm;
    }
    $form[$machineName . '_' . $index] = $sectionForm;
  }

  /**
   * Builds a set of fields to go inside a form section.
   *
   * A form section for general, email, or phone number will have the ability to
   * add more than one item, so this is called more than once for a form
   * section.
   */
  private function buildFieldSection($index, $verboseName, $verboseNameLower, $machineName, $labelFieldLabel, $valueFieldLabel, $valueFieldType, $valueFieldSize, $storedValues, array &$form) {
    $form[$machineName . '_' . $index . '_label'] = [
      '#type' => 'textfield',
      // '#size' => 32,
      '#title' => $this->t($labelFieldLabel),
      '#default_value' => $storedValues[$index]['label'] ?? '',
    ];
    $value = $storedValues[$index]['value'];
    $fieldName = $machineName . '_' . $index . '_value';
    $form[$fieldName] = [
      '#type' => $valueFieldType,
      // '#size' => $valueFieldSize,
      '#title' => $this->t($valueFieldLabel),
      '#default_value' => is_array($value) ? $value['value'] : $value,
      '#states' => [
        'required' => [
          ':input[name="' . $machineName . '_0_visible"]' => ['checked' => TRUE],
        ],
      ],
    ];
    if ($valueFieldType == 'text_format') {
      $form[$fieldName]['#format'] = $value['format'] ?? '';
      $form[$fieldName]['#base_type'] = 'textarea';
      $form[$fieldName]['#states']['required'] = [];
    }
    elseif ($index > 0) {
      $form[$fieldName]['#states']['required'][] = 'and';
      $form[$fieldName]['#states']['required'][':input[name="' . $machineName . '_' . $index . '_visible"]'] = ['checked' => TRUE];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $formValues = $form_state->getValues();
    $config = $this->config('ucb_site_configuration.contact_info');
    $fieldNames = ['visible', 'label', 'value'];
    $generalStoredValues = $config->get('general');
    $emailStoredValues = $config->get('email');
    $phoneStoredValues = $config->get('phone');
    $this->saveFormSection($formValues, $config, 'general', $fieldNames, count($generalStoredValues));
    $this->saveFormSection($formValues, $config, 'email', $fieldNames, count($emailStoredValues));
    $this->saveFormSection($formValues, $config, 'phone', $fieldNames, count($phoneStoredValues));
    // Set icons setting and save configuration.
    $config
      ->set('icons_visible', $formValues['icons_visible'])
      ->save();
    // Clear the cache so the new information will display in the site footer right away.
    // Not the recommended way to do this, but I tried cache tags and that did not work.
    \Drupal::service('cache.render')->invalidateAll();
  }

  /**
   * Saves the values of a section for general, email, or phone number.
   */
  private static function saveFormSection($formValues, $config, $sectionName, $fieldNames, $itemCount) {
    // Gather all primary / secondary fields from the form into one array.
    $values = [];
    for ($index = 0; $index < $itemCount; $index++) {
      $fieldNameValueDict = [];
      foreach ($fieldNames as $fieldName) {
        $fieldNameValueDict[$fieldName] = $formValues[$sectionName . '_' . $index . '_' . $fieldName];
      }
      $values[] = $fieldNameValueDict;
    }
    // The form design necessitates hiding all items if the primary one is not shown.
    $categoryVisible = $values[0]['visible'];
    // Set the configuration.
    $config
      ->set($sectionName . '_visible', $categoryVisible)
      ->set($sectionName, $values);
  }

}
