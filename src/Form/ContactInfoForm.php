<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Form\ContactInfoForm.
 */

namespace Drupal\ucb_site_configuration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ContactInfoForm extends ConfigFormBase {

	/**
	 * @return string
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
		// Toggle for icons
		$generalStoredValues = $config->get('general');
		$emailStoredValues = $config->get('email');
		$phoneStoredValues = $config->get('phone');
		if($generalStoredValues)
			$this->_buildFormSection(sizeof($generalStoredValues), 'Contact information', 'contact information', 'general', 'Label (optional)', 'Value', 'text_format', 255, $generalStoredValues, $form);
		if($emailStoredValues)
			$this->_buildFormSection(sizeof($emailStoredValues), 'Email address', 'email address', 'email', 'Label (optional)', 'Value', 'email', 20, $emailStoredValues, $form);
		if($phoneStoredValues)
			$this->_buildFormSection(sizeof($phoneStoredValues), 'Phone number', 'phone number', 'phone', 'Label (optional)', 'Value', 'tel', 20, $phoneStoredValues, $form);
		$form['icons_visible'] = [
			'#type' => 'checkbox',
			'#title' => $this->t('Show email and / or phone icons in the site footer'),
			'#default_value' => $config->get('icons_visible') ?? TRUE
		];
		return parent::buildForm($form, $form_state);
	}

	private function _buildFormSection($itemCount, $verboseName, $verboseNameLower, $machineName, $labelFieldLabel, $valueFieldLabel, $valueFieldType, $valueFieldSize, $storedValues, array &$form) {
		// Toggle for "Add primary x"
		$form[$machineName . '_0_visible'] = [
			'#type' => 'checkbox',
			'#title' => $this->t('Add primary ' . $verboseNameLower),
			'#default_value' => $storedValues[0]['visible'] ?? FALSE
		];
		// Section "Primary x"
		$sectionForm = [
			'#type' => 'details',
			'#title' => $this->t('Primary ' . $verboseNameLower),
			'#open' => TRUE,
			'#states' => [
				'visible' => [
					':input[name="' . $machineName . '_0_visible"]' => ['checked' => TRUE]
				]
			]
		];
		// Fields for primary item
		$this->_buildFieldSection(0, $verboseName, $verboseNameLower, $machineName, $labelFieldLabel, $valueFieldLabel, $valueFieldType, $valueFieldSize, $storedValues, $sectionForm);
		// Add secondary items
		for ($index = 1; $index < $itemCount; $index++) {
			// Toggle for "Add another x"
			$sectionForm[$machineName . '_' . $index . '_visible'] = [
				'#type' => 'checkbox',
				'#title' => $this->t('Add another ' . $verboseNameLower),
				'#default_value' => $storedValues[$index]['visible'] ?? FALSE
			];
			// Section "[another] x"
			$subSectionForm = [
				'#type' => 'details',
				'#title' => $this->t($verboseName),
				'#open' => TRUE,
				'#states' => [
					'visible' => [
						':input[name="' . $machineName . '_' . $index . '_visible"]' => ['checked' => TRUE]
					]
				]
			];
			// Fields for secondary item
			$this->_buildFieldSection($index, $verboseName, $verboseNameLower, $machineName, $labelFieldLabel, $valueFieldLabel, $valueFieldType, $valueFieldSize, $storedValues, $subSectionForm);
			$sectionForm[$machineName . '_' . $index] = $subSectionForm;
		}
		$form[$machineName . '_' . $index] = $sectionForm;
	}

	private function _buildFieldSection($index, $verboseName, $verboseNameLower, $machineName, $labelFieldLabel, $valueFieldLabel, $valueFieldType, $valueFieldSize, $storedValues, array &$form) {
		$form[$machineName . '_' . $index . '_label'] = [
			'#type' => 'textfield',
			// '#size' => 32,
			'#title' => $this->t($labelFieldLabel),
			'#default_value' => $storedValues[$index]['label'] ?? ''
		];
		$value = $storedValues[$index]['value'];
		if(is_array($value)) {
			$fieldDefaultFormat = $value['format'];
			$fieldDefaultValue = $value['value'];
		} else $fieldDefaultValue = $value;
		$form[$machineName . '_' . $index . '_value'] = [
			'#type' => $valueFieldType,
			// '#size' => $valueFieldSize,
			'#title' => $this->t($valueFieldLabel),
			'#default_value' => $fieldDefaultValue,
			'#states' => [
				'required' => [
					':input[name="' . $machineName . '_0_visible"]' => ['checked' => TRUE],
					'and',
					':input[name="' . $machineName . '_' . $index . '_visible"]' => ['checked' => TRUE]
				]
			]
		];
		if(isset($fieldDefaultFormat))
			$form[$machineName . '_' . $index . '_value']['#format'] = $fieldDefaultFormat;
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
		$this->_saveFormSection($formValues, $config, 'general', $fieldNames, sizeof($generalStoredValues));
		$this->_saveFormSection($formValues, $config, 'email', $fieldNames, sizeof($emailStoredValues));
		$this->_saveFormSection($formValues, $config, 'phone', $fieldNames, sizeof($phoneStoredValues));
		// Set icons setting and save configuration
		$config
			->set('icons_visible', $formValues['icons_visible'])
			->save();
		// Clear the cache so the new information will display in the site footer right away.
		// Not the recommended way to do this, but I tried cache tags and that did not work.
		\Drupal::service('cache.render')->invalidateAll();
	}

	private static function _saveFormSection($formValues, $config, $sectionName, $fieldNames, $itemCount) {
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
