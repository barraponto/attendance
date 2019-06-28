<?php

namespace Drupal\attendance\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides block plugin definitions for attendance types.
 *
 * @see \Drupal\attendance\Plugin\Block\AttendanceFormBlock
 */
class AttendanceFormBlock extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The attendance type storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Constructs new AttendanceFormBlock.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The attendance_type storage.
   */
  public function __construct(EntityStorageInterface $storage) {
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity.manager')->getStorage('attendance_type')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->storage->loadMultiple() as $bundle => $attendance_type) {
      $this->derivatives[$bundle] = $base_plugin_definition;
      $this->derivatives[$bundle]['admin_label'] = new TranslatableMarkup('@attendance_type form block', ['@attendance_type' => $attendance_type->label()]);
      $this->derivatives[$bundle]['config_dependencies']['config'] = [$attendance_type->getConfigDependencyName()];
    }
    return $this->derivatives;
  }

}
