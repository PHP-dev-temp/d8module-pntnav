<?php
/**
* Provides a 'Prev-Next' Block
*
* @Block(
*   id = "pntnav_block",
*   admin_label = @Translation("Prev-Nav title block"),
* )
*/

namespace Drupal\pntnav\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\pntnav\PntnavHelper;

class PntnavBlock extends BlockBase implements BlockPluginInterface {
  /**
  * {@inheritdoc}
  */
  public function build() {

    // Get sorting criteria.
    $config = $this->getConfiguration();
    if (!empty($config['nav_sorting'])) {
      $nav_sorting = $config['nav_sorting'];
    }
    else {
      $nav_sorting = 'created';
    }

    // Get prev/next links.
    $pntnav = new PntnavHelper(\Drupal::routeMatch());
    $pntnav->calculate($nav_sorting);
    $prev = $pntnav->prev;
    $next = $pntnav->next;

    // Create output markup and prevent cashing.
    $output = array(
      '#cache' => array ('max-age' => 0), // No cache
      '#theme' => 'pntnav',
      '#prevtitle' => $prev,
      '#nexttitle' => $next,
    );
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Add configuration options.
    $config = $this->getConfiguration();
    $form['pntnav_nav_sorting'] = array (
        '#type' => 'radios',
        '#title' => $this->t('Choose sorting criteria:'),
        '#default_value' => isset($config['nav_sorting']) ? $config['nav_sorting'] : 'created',
        '#options' => array('title' => 'title', 'created' => 'created'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('nav_sorting', $form_state->getValue('pntnav_nav_sorting'));
  }
}
