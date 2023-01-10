<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Controller\HelpController.
 */

namespace Drupal\ucb_site_configuration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;

class HelpController extends ControllerBase {
	/**
	 * @return \Drupal\Core\Routing\TrustedRedirectResponse
	 *   A redirect to the CU Boulder web support site.
	 */
	public function helpRedirect() {
		return new TrustedRedirectResponse('https://websupport.colorado.edu/');
	}
}
