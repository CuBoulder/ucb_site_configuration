<?php

namespace Drupal\ucb_site_configuration\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * The access controller for the individual tabs of CU Boulder site settings.
 *
 * Tabs that only require one access permission need not be defined here, as the
 * permission can instead be defined in `ucb_site_configuration.routing.yml`.
 */
class SiteSettingsAccessController {

  /**
   * Gets an access result for the "General" tab.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account interface of the requesting user.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result for the "General" tab of CU Boulder site settings.
   */
  public function accessGeneral(AccountInterface $account) {
    return AccessResult::allowedIf($account->hasPermission('edit ucb site general') || $account->hasPermission('edit ucb site pages'));
  }

}
