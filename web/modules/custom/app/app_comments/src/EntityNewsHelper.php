<?php


namespace Drupal\app_comments;


use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;

class EntityNewsHelper {


  public static function currentUserView() {
    $currentUser = \Drupal::currentUser();
    $entity_type = 'user';
    $view_mode = 'teaser';
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
    try {
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
      $user = $storage->load($currentUser->id());
      return $view_builder->view($user, $view_mode);
    } catch (InvalidPluginDefinitionException $e) {
    } catch (PluginNotFoundException $e) {
    }

  }

}