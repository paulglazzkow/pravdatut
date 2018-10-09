<?php


namespace Drupal\app_comments;


use Drupal\ajax_comments\Utility;
use Drupal\comment\CommentInterface;
use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Url;

class LinkAnswer {

  public static function add(array &$build, CommentInterface $comment, EntityViewDisplayInterface $display) {
    if ($display->getComponent('link_answer')) {
      $build['link_answer'] = self::build($comment);
    }
  }

  private static function build(CommentInterface $comment) {
    $commented_entity = $comment->getCommentedEntity();
    $links = [];
    $status = $commented_entity->get($comment->getFieldName())->status;

    if ($status == CommentItemInterface::OPEN) {
      //      $context = \Drupal::service('context.handler')->getActiveContexts();
      if ($comment->access('create')) {
        $field_name = $comment->getFieldName();
        $wrapper_html_id = Utility::getWrapperIdFromEntity($commented_entity, $field_name);

        $classes = [
          'use-ajax',
          'js-use-ajax-comments',
          'js-ajax-comments-reply',
          'js-ajax-comments-reply-' . $comment->getCommentedEntityId() . '-' . $comment->getFieldName() . '-' . $comment->id(),
        ];


        $links['comment-reply'] = [
          'title' => t('Reply'),
          'attributes' => ['class' => $classes, 'data-wrapper-html-id' => $wrapper_html_id],
          'url' => Url::fromRoute(
            'ajax_comments.reply',
            [
              'entity_type' => $comment->getCommentedEntityTypeId(),
              'entity' => $comment->getCommentedEntityId(),
              'field_name' => $comment->getFieldName(),
              'pid' => $comment->id(),
            ]
          ),
        ];

      }

    }

    return [
      '#theme' => 'links',
      // The "entity" property is specified to be present, so no need to check.
      '#links' => $links,
      '#attributes' => ['class' => ['links', 'inline']],
    ];
  }
}