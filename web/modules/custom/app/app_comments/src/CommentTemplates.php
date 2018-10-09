<?php


namespace Drupal\app_comments;


use Exception;

class CommentTemplates {

  public static function override(&$theme_registry) {
    $templates = [
      'comment__field_comments__news' => 'comment--field-comments',
      'field_comment' => 'field--field-comments',
    ];
    $module_path = drupal_get_path('module', 'app_comments');
    foreach ($templates as $theme => $template) {
      if (FALSE === isset($theme_registry[$theme])) {
        continue;
      }
      $theme_registry[$theme]['theme path'] = $module_path;
      $theme_registry[$theme]['path'] = $module_path . '/templates';
      $theme_registry[$theme]['template'] = $template;
    }
  }

  public static function createDropdownLinks(&$links) {

    $links['#attributes']['class'][] = 'menu';
    try {
      //    unset($links['comment']['#links']['comment-reply']);
      $filtered = [];
      $names = ['comment-edit', 'comment-delete'];
      foreach ($names as $name) {
        if (FALSE === isset($links['comment']['#links'][$name])) {
          continue;
        }
        $filtered[$name] = $links['comment']['#links'][$name];
      }
      $links['comment']['#links'] = $filtered;
    } catch (Exception $err) {

    }

    if (!empty($links['comment']['#links'])) {
      $links['button'] = [
        '#type' => 'button',
        '#value' => '<i class="fas fa-bars"></i>',
        '#attributes' => [
          'class' => ['button'],
          'data-toggle' => '',
        ],
      ];
    }
  }

  public static function createFooterLinks(&$variables) {

    if (isset($variables['content']['links'])) {
      $variables['links'] = $variables['content']['links'];
      unset($variables['content']['links']);

    }

    $footer_fields = [
      'link_answer',
      'flag_comment_like',
      'flag_comment_dislike',
      'flag_comment_abuse',
    ];

    $footer = ['#theme' => 'item_list', '#items' => []];
    foreach ($footer_fields as $field_name) {
      //  foreach (array_keys($variables['content']) as $field_name) {
      if (!isset($variables['content'][$field_name])) {
        continue;
      }
      switch ($field_name) {
        case 'link_answer':
          if (isset($variables['content'][$field_name]['#links']['comment-reply'])) {
            $link = $variables['content'][$field_name]['#links']['comment-reply'];
            $variables['content'][$field_name]['#attributes']['class'] = ['menu'];
            $variables['content'][$field_name] = [
              '#type' => 'link',
              '#title' => $link['title'],
              '#url' => $link['url'],
              '#options' => ['attributes' => $link['attributes']],
            ];

          }
          break;
      }
      $footer['#items'][$field_name] = $variables['content'][$field_name];
      unset($variables['content'][$field_name]);
    }

    if ($footer) {
      $footer['#attributes'] = ['class' => ['comment-footer-links']];
      $variables['content_footer'] = $footer;
    }
  }

}