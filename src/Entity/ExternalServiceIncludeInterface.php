<?php

/**
 * @file
 * Contains Drupal\ucb_site_configuration\Entity\ExternalServiceIncludeInterface.
 */

namespace Drupal\ucb_site_configuration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface ExternalServiceIncludeInterface extends ConfigEntityInterface {

	/**
	 * {@inheritdoc}
	 * 
     * @return string
	 *   The internal id of this ExternalServiceInclude.
	 */
	public function id();

	/**
	 * {@inheritdoc}
	 * 
	 * @return string
	 *   The label of this ExternalServiceInclude.
	 */
	public function label();

	/**
	 * Gets the name of the service of this ExternalServiceInclude.
	 *
	 * @return string
	 *   The name of the service of this ExternalServiceInclude.
	 */
	public function serviceName();
}
