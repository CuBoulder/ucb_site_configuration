<?php

namespace Drupal\ucb_site_configuration\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The "site info" block, meant to be placed in the site footer.
 *
 * @Block(
 *   id = "site_info",
 *   admin_label = @Translation("Site Contact Info Footer"),
 * )
 */
class SiteInfoBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The connfig factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configFactory->get('ucb_site_configuration.contact_info');
    $general = array_map(function ($item) {
      return [
        'visible' => $item['visible'],
        'label' => $item['label'],
        'value' => [
          '#type' => 'processed_text',
          '#text' => $item['value']['value'],
          '#format' => $item['value']['format'],
          '#langcode' => 'en',
        ],
      ];
    }, $config->get('general'));
    return [
      '#data' => [
        'icons_visible' => $config->get('icons_visible'),
        'general_visible' => $config->get('general_visible'),
        'general' => $general,
        'email_visible' => $config->get('email_visible'),
        'email' => $config->get('email'),
        'phone_visible' => $config->get('phone_visible'),
        'phone' => $config->get('phone'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

}
