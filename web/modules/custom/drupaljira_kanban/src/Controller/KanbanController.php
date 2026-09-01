<?php

namespace Drupal\drupaljira_kanban\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for Kanban board AJAX actions.
 */
class KanbanController extends ControllerBase {

  /**
   * Updates task status via AJAX.
   *
   * @param object|null $node
   *   The node entity.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response containing status update result.
   */
  public function updateStatus($node, Request $request) {
    if (!$node) {
      return new JsonResponse(['success' => FALSE, 'error' => 'Task not found'], 404);
    }

    $data = json_decode($request->getContent(), TRUE);
    $new_status = $data['status'] ?? NULL;

    if ($new_status) {
      $node->set('field_status', $new_status);
      $node->save();
      return new JsonResponse(['success' => TRUE]);
    }

    return new JsonResponse(['success' => FALSE, 'error' => 'Invalid status'], 400);
  }

}
