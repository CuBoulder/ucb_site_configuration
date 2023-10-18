<?php

namespace Drupal\ucb_site_configuration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * The entity used to add a node-level third-party service service to a site.
 *
 * This is distinct from block-level third-party services, or third-party
 * services such as Google Maps that can be added from a plugin in the
 * WYSIWYG editor, which aren't handled by this module.
 */
interface ExternalServiceIncludeInterface extends ConfigEntityInterface {

  /**
   * Gets the machine name of this include.
   *
   * @return string
   *   The machine name of this include.
   */
  public function id();

  /**
   * Gets the label of this include.
   *
   * @return string
   *   The label of this include.
   */
  public function label();

  /**
   * Gets the name of the service this include is intended to provide.
   *
   * @return string
   *   The name of the service this include is intended to provide.
   */
  public function getServiceName();

  /**
   * Gets the settings of the service of this include.
   *
   * @return array
   *   The settings of the service of this include.
   */
  public function getServiceSettings();

  /**
   * Gets if this include applies to the entire site rather than specific nodes.
   *
   * @return bool
   *   TRUE if this include applies to the entire site rather than specific
   *   nodes, FALSE if not.
   */
  public function isSitewide();

  /**
   * Gets if this include can be added or removed by content authors.
   *
   * @return bool
   *   TRUE if this include can be added or removed by content authors, FALSE
   *   if not.
   */
  public function isContentEditingEnabled();

  /**
   * Gets the ids for nodes that have been explicitly included.
   *
   * @return string[]
   *   The ids for nodes that have been explicitly included in the "Content"
   *   field.
   */
  public function getNodeIds();

  /**
   * Gets the nodes that this include applies to.
   *
   * @return \Drupal\node\NodeInterface[]
   *   The nodes that this include applies to.
   */
  public function getNodes();

}
