<?php

namespace Drupal\ucb_site_configuration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\node\Entity\Node;

/**
 * The entity used to add a node-level third-party service service to a site.
 *
 * This is distinct from block-level third-party services, or third-party
 * services such as Google Maps that can be added from a plugin in the
 * WYSIWYG editor, which aren't handled by this module.
 *
 * @ConfigEntityType(
 *  id = "ucb_external_service_include",
 *  label = @Translation("Third-party service"),
 *  handlers = {
 *   "view_builder" = "Drupal\Core\Config\Entity\ConfigEntityViewBuilder",
 *   "list_builder" = "Drupal\ucb_site_configuration\Controller\ExternalServiceIncludeListBuilder",
 *   "form" = {
 *    "add" = "Drupal\ucb_site_configuration\Form\ExternalServiceIncludeEntityForm",
 *    "edit" = "Drupal\ucb_site_configuration\Form\ExternalServiceIncludeEntityForm",
 *    "delete" = "Drupal\ucb_site_configuration\Form\ExternalServiceIncludeEntityDeleteForm"
 *   }
 *  },
 *  config_prefix = "external_service_includes",
 *  base_table = "ucb_external_service_includes",
 *  admin_permission = "administer ucb external services",
 *  entity_keys = {
 *   "id" = "id",
 *   "label" = "label",
 *   "service_name" = "service_name",
 *   "service_settings" = "service_settings",
 *   "sitewide" = "sitewide",
 *   "content_editing_enabled" = "content_editing_enabled",
 *   "nodes" = "nodes"
 *  },
 *  config_export = {
 *   "id",
 *   "label",
 *   "service_name",
 *   "service_settings",
 *   "sitewide",
 *   "content_editing_enabled",
 *   "nodes"
 *  },
 *  links = {
 *   "collection" = "/admin/config/cu-boulder/services",
 *   "edit-form" = "/admin/config/cu-boulder/services/{ucb_external_service_include}",
 *   "delete-form" = "/admin/config/cu-boulder/services/{ucb_external_service_include}/delete"
 *  }
 * )
 */
class ExternalServiceInclude extends ConfigEntityBase implements ExternalServiceIncludeInterface {

  /**
   * The machine name of this include.
   *
   * @var string
   */
  protected $id = '';

  /**
   * The label of this include.
   *
   * @var string
   */
  protected $label = '';

  /**
   * The name of the service of this include.
   *
   * @var string
   */
  protected $service_name = '';

  /**
   * The settings of the service of this include.
   *
   * @var array
   */
  protected $service_settings = [];

  /**
   * True if this include applies to the entire site rather than specific nodes.
   *
   * @var bool
   */
  protected $sitewide = FALSE;

  /**
   * True if this include can be added or removed by content authors.
   *
   * @var bool
   */
  protected $content_editing_enabled = TRUE;

  /**
   * The ids for nodes that this include applies to.
   *
   * @var string[]
   */
  protected $nodes = [];

  /**
   * {@inheritdoc}
   */
  public function getServiceName() {
    return $this->service_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getServiceSettings() {
    return $this->service_settings;
  }

  /**
   * {@inheritdoc}
   */
  public function isSitewide() {
    return (bool) $this->sitewide;
  }

  /**
   * {@inheritdoc}
   */
  public function isContentEditingEnabled() {
    return (bool) $this->content_editing_enabled;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeIds() {
    return $this->nodes;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodes() {
    return Node::loadMultiple($this->nodes);
  }

}
