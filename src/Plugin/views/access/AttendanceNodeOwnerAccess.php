<?php

namespace Drupal\attendance\Plugin\views\access;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\views\Plugin\views\access\AccessPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

/**
 * Access plugin that provides attended node owner access control.
 *
 * @ingroup views_access_plugins
 *
 * @ViewsAccess(
 *  id = "attendance_node_owner",
 *  title = @Translation("Attended node owner"),
 *  help = @Translation("Access will be granted to the attended node owner."),
 * )
 */
class AttendanceNodeOwnerAccess extends AccessPluginBase implements CacheableDependencyInterface {

  /**
   * The node entity from the route.
   *
   * @var \Drupal\node\Entity\NodeInterface
   */
  protected $node;

  /**
   * The node context from the route.
   *
   * @var \Drupal\Core\Plugin\Context\ContextInterface
   */
  protected $context;

  /**
   * The access check.
   *
   * @var \Drupal\Core\Routing\Access\AccessInterface
   */
  protected $check;

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    if (!empty($this->node)) {
      return $this->check->access($account, $this->node)->isAllowed();
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function summaryTitle() {
    return $this->t('Allowed for attended node owner.');
  }

  /**
   * {@inheritdoc}
   */
  public function alterRouteDefinition(Route $route) {
    $route->setRequirement('_owns_attended_node', 'TRUE');
    $route->setOption('parameters', ['node' => ['type' => 'entity:node']]);
  }

  /**
   * Constructs a ViewsAccess plugin.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Plugin\Context\ContextProviderInterface $context_provider
   *   The node route context.
   * @param \Drupal\Core\Routing\Access\AccessInterface $access
   *   The attended node owner access check.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ContextProviderInterface $context_provider, AccessInterface $access) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $contexts = $context_provider->getRuntimeContexts(['node']);
    $this->context = $contexts['node'];
    $this->node = $this->context->getContextValue();
    $this->check = $access;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('node.node_route_context'),
      $container->get('access_check.attended_node_owner')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::mergeMaxAges(Cache::PERMANENT, $this->context->getCacheMaxAge());
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return $this->context->getCacheContexts();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return $this->context->getCacheTags();
  }

}
