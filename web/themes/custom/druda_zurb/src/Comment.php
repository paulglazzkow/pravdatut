<?php


namespace Drupal\druda_zurb;


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
    if ($url) {
      $url = $url->toString();

    }

    return ['#markup' => "<a href=\'{$url}\'>{$text}</a>"];
  }
}