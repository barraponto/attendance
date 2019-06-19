<?php

namespace Drupal\attendance\Plugin\views\access;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
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
   * @var \Drupal\group\Entity\GroupInterface
   */
  protected $node;

  /**
   * The node context from the route.
   *
   * @var \Drupal\Core\Plugin\Context\ContextInterface
   */
  protected $context;

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    $user_id = $this->account->id();
    return ($user_id == 1) || ($this->node->getOwnerId() == $user_id);
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
    $route->setRequirement('_access', 'TRUE');
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
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user from current session.
   * @param \Drupal\Core\Plugin\Context\ContextProviderInterface $context_provider
   *   The node route context.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $account, ContextProviderInterface $context_provider) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $contexts = $context_provider->getRuntimeContexts(['node']);
    $this->account = $account;
    $this->context = $contexts['node'];
    $this->node = $this->context->getContextValue();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('node.node_route_context')
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
