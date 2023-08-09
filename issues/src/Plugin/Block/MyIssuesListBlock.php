<?php

namespace Drupal\issues\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\issues\Plugin\Block\Cache;


/**
 * Provides a my issues list block.
 *
 * @Block(
 *   id = "issues_my_issues_list",
 *   admin_label = @Translation("My issues list"),
 *   category = @Translation("Custom"),
 * )
 */
final class MyIssuesListBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build(): array
    {
        $currentUserId = \Drupal::currentUser()->id();
        $nids = \Drupal::entityQuery('node')
            ->accessCheck(true)
            ->condition('type', 'issues')
            ->condition('custom_user_reference', $currentUserId)
            ->sort('created', 'DESC')
            ->pager(3)
            ->execute();
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
        $node_links = [];
        foreach ($nodes as $node) {
            $node_links[] = [
            '#type' => 'link',
            '#title' => $node->getTitle(),
            '#url' => $node->toUrl(),
            ];
        }
        // Example cache tag for a specific content type.
        $cache_tags = ['node_type:issues'];

        $content = [
        '#theme' => 'item_list',
        '#items' => $node_links,
        // Cache the block content.
        '#cache' => [
        'max-age' => 0,
          ],
        ];

        return $content;
    }

}
