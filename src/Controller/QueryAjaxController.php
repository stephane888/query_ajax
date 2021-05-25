<?php

namespace Drupal\query_ajax\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Query ajax routes.
 */
class QueryAjaxController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
