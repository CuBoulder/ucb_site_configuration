<?php

namespace Drupal\ucb_site_configuration\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\ucb_site_configuration\SiteConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The page for the "Third-party services" tab in CU Boulder site settings.
 */
class ExternalServiceIncludeListBuilder extends ConfigEntityListBuilder {

  /**
   * The site configuration service defined in this module.
   *
   * @var \Drupal\ucb_site_configuration\SiteConfiguration
   */
  protected $service;

  /**
   * Constructs an ExternalServiceIncludeListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\ucb_site_configuration\SiteConfiguration $service
   *   The site configuration service defined in this module.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, SiteConfiguration $service) {
    parent::__construct($entity_type, $storage);
    $this->service = $service;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
    $entity_type,
    $container->get('entity_type.manager')->getStorage($entity_type->id()),
    $container->get('ucb_site_configuration')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['service_name'] = $this->t('Service');
    $header['label'] = $this->t('Label');
    $header['included_on'] = $this->t('Included on');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\ucb_site_configuration\Entity\ExternalServiceIncludeInterface $entity
   *   The ExternalServiceInclude entity.
   */
  public function buildRow(EntityInterface $entity) {
    $externalServiceConfiguration = $this->service->getConfiguration()->get('external_services')[$entity->getServiceName()];
    $row['service_name'] = $externalServiceConfiguration['label'] ?? $entity->getServiceName();
    $row['label'] = $entity->label();
    $row['included_on'] = $entity->isSitewide() ? $this->t('All pages') : $this->formatPlural(count($entity->getNodes()), '1 page', '@count pages');
    return $row + parent::buildRow($entity);
  }

}
