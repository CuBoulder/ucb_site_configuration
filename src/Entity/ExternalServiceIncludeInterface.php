<?php

/**
 * @file
 * Contains Drupal\ucb_site_configuration\Entity\ExternalServiceIncludeInterface.
 */

namespace Drupal\ucb_site_configuration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface ExternalServiceIncludeInterface extends ConfigEntityInterface {

	/**
     * @return string
	 *   The machine name of this include.
	 */
	public function id();

	/**
	 * @return string
	 *   The label of this include.
	 */
	public function label();

	/**
	 * @return string
	 *   The name of the service this include is intended to provide.
	 */
	public function getServiceName();


	/**
	 * @return array
	 *   The settings of the service of this include.
	 */
	public function getServiceSettings();

	/**
	 * @return boolean
	 *   TRUE if this include applies to the entire site rather than specific nodes, FALSE if not.
	 */
	public function isSitewide();

	/**
	 * @return boolean
	 *   TRUE if this include can be added or removed by content authors, FALSE if not.
	 */
	public function isContentEditingEnabled();

	/**
	 * @return string[]
	 *   The ids for nodes that have been explicitly included in the "Content" field.
	 */
	public function getNodeIds();

	/**
	 * @return \Drupal\node\NodeInterface[]
	 *   All the nodes that this include applies to.
	 */
	public function getNodes();
}
