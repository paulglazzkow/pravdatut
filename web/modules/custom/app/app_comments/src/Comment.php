<?php


namespace Drupal\app_comments;


use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;

class Comment {

  public static function getPermalink($variables) {
    /* @var $comment \Drupal\comment\Entity\Comment */
    $comment = $variables['elements']['#comment'];
    $text = '<i class="fas fa-anchor"></i>';
    if (isset($comment->in_preview)) {
      $url = new Url('<front>');
    }
    else {
      $url = $comment->permalink();
    }

    return [
      '#type' => 'link',
      '#title' => Markup::create($text),
      '#url' => $url,
    ];
  }
}