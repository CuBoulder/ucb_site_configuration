<?php

/**
 * @file
 * Contains \Drupal\ucb_site_configuration\Plugin\Block\SiteInfoBlock.
 */

namespace Drupal\ucb_site_configuration\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * @Block(
 *   id = "site_info",
 *   admin_label = @Translation("Site Contact Info Footer"),
 * )
 */
class SiteInfoBlock extends BlockBase {
	/**
	 * {@inheritdoc}
	 */
	public function build() {
		$config = \Drupal::config('ucb_site_configuration.contact_info');
		return [
			'#data' => [
				'icons_visible' => $config->get('icons_visible'),
				'address_visible' => $config->get('address_visible'),
				'address' => $config->get('address'),
				'email_visible' => $config->get('email_visible'),
				'email' => $config->get('email'),
				'phone_visible' => $config->get('phone_visible'),
				'phone' => $config->get('phone')
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function blockAccess(AccountInterface $account) {
		return AccessResult::allowedIfHasPermission($account, 'access content');
	}

	/**
	 * {@inheritdoc}
	 */
	public function blockForm($form, FormStateInterface $form_state) {
		return parent::blockForm($form, $form_state);
	}

	/**
	 * {@inheritdoc}
	 */
	public function blockSubmit($form, FormStateInterface $form_state) {
		return parent::blockSubmit($form, $form_state);
	}
}
