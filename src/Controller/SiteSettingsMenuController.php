<?php

/**
 * @file
 * Contains Drupal\ucb_site_configuration\Controller\SiteSettingsMenuController.
 */

namespace Drupal\ucb_site_configuration\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\ucb_admin_menus\Controller\OverviewController;

class SiteSettingsMenuController extends OverviewController {
	/**
	 * Checks if a user can access the "CU Boulder site settings" administration menu.
	 *
	 * @param \Drupal\Core\Session\AccountInterface $account
	 *   The current user.
	 *
	 * @return \Drupal\Core\Access\AccessResultInterface
	 *   The access result.
	 */
	public function access(AccountInterface $account) {
		return AccessResult::allowedIf($account->hasPermission('access administration pages') && ($account->hasPermission('edit ucb site general') || $account->hasPermission('edit ucb site appearance') || $account->hasPermission('edit ucb site contact info') || $account->hasPermission('administer ucb external services')));
	}
}
