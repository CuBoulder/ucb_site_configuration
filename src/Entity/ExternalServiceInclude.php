<?php

/**
 * @file
 * Contains Drupal\ucb_site_configuration\Entity\ExternalServiceInclude.
 */

namespace Drupal\ucb_site_configuration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the ExternalServiceInclude entity.
 * 
 * @ConfigEntityType(
 *  id = "ucb_external_service_include",
 *  label = @Translation("Third-party service include"),
 *  handlers = {
 *   "view_builder" = "\Drupal\Core\Config\Entity\ConfigEntityViewBuilder",
 *   "list_builder" = "\Drupal\Core\Config\Entity\ConfigEntityListBuilder",
 *   "form" = {
 *    "add" = "Drupal\ucb_site_configuration\Form\ExternalServiceIncludeEntityForm",
 *    "edit" = "Drupal\ucb_site_configuration\Form\ExternalServiceIncludeEntityForm",
 *    "delete" = "Drupal\ucb_site_configuration\Form\ExternalServiceIncludeEntityDeleteForm" 
 *   }
 *  },
 *  config_prefix = "external_service_includes",
 *  base_table = "ucb_external_service_includes",
 *  admin_permission = "administer ucb site",
 *  entity_keys = {
 *   "id" = "id",
 *   "label" = "label",
 *   "service_name" = "service_name"
 *  },
 *  config_export = {
 *   "id",
 *   "label",
 *   "service_name"
 *  },
 *  links = {
 *   "collection" = "/admin/config/cu-boulder/services/includes",
 *   "edit-form" = "/admin/config/cu-boulder/services/includes/{ucb_external_service_include}",
 *   "delete-form" = "/admin/config/cu-boulder/services/includes/{ucb_external_service_include}/delete"
 *  }
 * )
 */
class ExternalServiceInclude extends ConfigEntityBase implements ExternalServiceIncludeInterface {

	/**
	 * The internal id of this ExternalServiceInclude.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The label of this ExternalServiceInclude.
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * The name of the service of this ExternalServiceInclude.
	 *
	 * @var string
	 */
	protected $service_name;

	/**
	 * {@inheritdoc}
	 */
	public function serviceName() {
		return $this->service_name;
	}
}
