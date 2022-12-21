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
	 * @return boolean
	 *   TRUE if this include applies to the entire site rather than specific nodes, FALSE if not.
	 */
	public function isSitewide();

	/**
	 * @return \Drupal\node\NodeInterface[]
	 *   The nodes that this include applies to.
	 */
	public function getNodes();
}
