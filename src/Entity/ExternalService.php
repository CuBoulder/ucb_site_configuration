<?php

/**
 * @file
 * Contains Drupal\ucb_site_configuration\Entity\ExternalService.
 */

namespace Drupal\ucb_site_configuration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the ExternalService entity.
 * 
 * @ConfigEntityType(
 *  id = "ucb_external_service",
 *  label = @Translation("Third-party service"),
 *  handlers = {
 *   "view_builder" = "\Drupal\Core\Config\Entity\ConfigEntityViewBuilder",
 *   "list_builder" = "\Drupal\Core\Config\Entity\ConfigEntityListBuilder",
 *   "form" = {
 *    "add" = "Drupal\ucb_site_configuration\Form\ExternalServiceEntityForm",
 *    "edit" = "Drupal\ucb_site_configuration\Form\ExternalServiceEntityForm",
 *    "delete" = "Drupal\ucb_site_configuration\Form\ExternalServiceEntityDeleteForm" 
 *   }
 *  },
 *  config_prefix = "external_services",
 *  list_cache_contexts = { "user" },
 *  base_table = "ucb_external_service",
 *  admin_permission = "administer ucb site",
 *  entity_keys = {
 *   "id" = "id",
 *   "label" = "label"
 *  },
 *  config_export = {
 *   "id",
 *   "label"
 *  },
 *  links = {
 *   "collection" = "/admin/config/cu-boulder/services/list",
 *   "edit-form" = "/admin/config/cu-boulder/services/{ucb_external_service}",
 *   "delete-form" = "/admin/config/cu-boulder/services/{ucb_external_service}/delete"
 *  }
 * )
 */
class ExternalService extends ConfigEntityBase {

	/**
	 * The internal id of this ExternalService.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The label of this ExternalService.
	 *
	 * @var string
	 */
	protected $label;
}
