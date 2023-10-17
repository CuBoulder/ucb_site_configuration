<?php

namespace Drupal\ucb_site_configuration\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\ucb_admin_menus\Controller\OverviewController;

/**
 * The access controller for the "CU Boulder site settings" administration menu.
 */
class SiteSettingsMenuController extends OverviewController {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowedIf($account->hasPermission('access administration pages') && ($account->hasPermission('edit ucb site general') || $account->hasPermission('edit ucb site appearance') || $account->hasPermission('edit ucb site contact info') || $account->hasPermission('administer ucb external services')));
  }

}
