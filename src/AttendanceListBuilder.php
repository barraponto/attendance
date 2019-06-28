<?php

namespace Drupal\attendance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Attendance entities.
 *
 * @ingroup attendance
 */
class AttendanceListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Attendance ID');
    $header['email'] = $this->t('Email');
    $header['attends'] = $this->t('Attends');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\attendance\Entity\Attendance $entity */
    $row['id'] = $entity->id();

    $row['email'] = Link::createFromRoute(
      $entity->get('email')->value,
      'entity.attendance.edit_form',
      ['attendance' => $entity->id()]
    );

    $nodes = $entity->get('attends')->referencedEntities();
    $node = reset($nodes);
    $row['attends'] = Link::createFromRoute(
      $node->label(),
      'entity.node.canonical',
      ['node' => $node->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
