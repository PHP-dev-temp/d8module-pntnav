<?php
/**
 * @file
 * Contains Drupal\pntnav\PntnavHelper.
 */

namespace Drupal\pntnav;

use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

class PntnavHelper
{

    public $node;
    public $prev;
    public $next;

    /**
     * PntnavHelper constructor.
     * @param RouteMatchInterface $route_match
     */
    public function __construct(RouteMatchInterface $route_match) {
        $this->node = $route_match->getParameter('node');
    }

    /**
     * Check if is node view.
     * @return bool
     */
    public function isNode() {
        return $this->node ? true : false;
    }

    /**
     * Get $nid from current node.
     * @return null
     */
    public function getCurrentNid() {
        $nid = null;
        if ($this->isNode()) {
            $nid = $this->node->get('nid')->value;
        }
        return $nid;
    }

    /**
     * Calculate previous and next node and create $prev and $next links.
     * @param $field
     */
    public function calculate($field)
    {
        $this->prev = null;
        $this->next = null;
        If ($this->isNode()){

            // Query nodes to get sorted list of nid.
            $currentNid = $this->getCurrentNid();
            $query = \Drupal::entityQuery('node');
            $results = $query
                ->condition('type', $this->node->getType())
                ->condition('status', '1')
                ->condition('langcode', $this->node->get('langcode')->value)
                ->sort($field, 'ASC')
                ->execute();

            // Check if we have results.
            if (!empty($results) && is_array($results)) {
                $results = array_values($results);
                $prev_nid = null;
                $next_nid = null;
                $status = 0;
                foreach ($results as $result) {
                    If ($status === 1) {
                        $next_nid = $result;
                        break;
                    }
                    if ($result == $currentNid) $status = 1;
                    If ($status === 0) $prev_nid = $result;
                }

                // Create previous node link.
                if ($prev_nid) {
                    $new_node = Node::load($prev_nid);
                    $new_title = $new_node->get('title')->value;

                    //Build the link.
                    $options = array('absolute' => TRUE);
                    $url = Url::fromRoute('entity.node.canonical', ['node' => $prev_nid], $options);
                    $this->prev = \Drupal::l($new_title, $url);
                }

                // Create next node link.
                if ($next_nid) {
                    $new_node = Node::load($next_nid);
                    $new_title = $new_node->get('title')->value;

                    //Build the link.
                    $options = array('absolute' => TRUE);
                    $url = Url::fromRoute('entity.node.canonical', ['node' => $next_nid], $options);
                    $this->next = \Drupal::l($new_title, $url);
                }
            }
        }
    }

}
