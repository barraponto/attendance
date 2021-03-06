<?php

namespace Drupal\attendance\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'AttendanceFormBlock' block.
 *
 * @Block(
 *  id = "attendance_form_block",
 *  admin_label = @Translation("Attendance form block"),
 *  category = @Translation("Attendance"),
 *  deriver = "\Drupal\attendance\Plugin\Derivative\AttendanceFormBlock",
 *  context_definitions = {
 *    "node" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *  }
 * )
 */
class AttendanceFormBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new AttendanceFormBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity Type Manager service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $storage = $this->entityTypeManager->getStorage('attendance');
    $attendance_type = $this->getDerivativeId();
    $nid = $this->getContextValue('node')->id();
    $user = \Drupal::currentUser();

    if ($user->id()) {
      $existing = $storage->loadByProperties([
        "type" => $attendance_type,
        "attends" => $nid,
        "user_id" => $user->id(),
      ]);
      $attendance = $existing ? reset($existing) : $storage->create([
        "type" => $attendance_type,
        "attends" => $nid,
        "user_id" => $user->id(),
      ]);
    }
    else {
      // @TODO: support sessions
      $attendance = $storage->create(["type" => $attendance_type, "attends" => $nid]);
    }

    $form = $this->entityTypeManager
      ->getFormObject('attendance', 'default')
      ->setEntity($attendance);

    $build = \Drupal::formBuilder()->getForm($form);
    $build['user_id']['#access'] = FALSE;
    $build['attends']['#access'] = FALSE;

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    $access_handler = $this->entityTypeManager->getAccessControlHandler('attendance');
    $attendance_type = $this->getDerivativeId();
    return $access_handler->createAccess($attendance_type, $account, [], TRUE);
  }

}
