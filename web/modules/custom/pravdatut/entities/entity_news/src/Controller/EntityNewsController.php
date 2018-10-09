<?php

namespace Drupal\entity_news\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\entity_news\Entity\EntityNewsInterface;

/**
 * Class EntityNewsController.
 *
 *  Returns responses for News routes.
 */
class EntityNewsController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a News  revision.
   *
   * @param int $entity_news_revision
   *   The News  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($entity_news_revision) {
    $entity_news = $this->entityManager()->getStorage('entity_news')->loadRevision($entity_news_revision);
    $view_builder = $this->entityManager()->getViewBuilder('entity_news');

    return $view_builder->view($entity_news);
  }

  /**
   * Page title callback for a News  revision.
   *
   * @param int $entity_news_revision
   *   The News  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($entity_news_revision) {
    $entity_news = $this->entityManager()->getStorage('entity_news')->loadRevision($entity_news_revision);
    return $this->t('Revision of %title from %date', ['%title' => $entity_news->label(), '%date' => format_date($entity_news->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a News .
   *
   * @param \Drupal\entity_news\Entity\EntityNewsInterface $entity_news
   *   A News  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EntityNewsInterface $entity_news) {
    $account = $this->currentUser();
    $langcode = $entity_news->language()->getId();
    $langname = $entity_news->language()->getName();
    $languages = $entity_news->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $entity_news_storage = $this->entityManager()->getStorage('entity_news');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity_news->label()]) : $this->t('Revisions for %title', ['%title' => $entity_news->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all news revisions") || $account->hasPermission('administer news entities')));
    $delete_permission = (($account->hasPermission("delete all news revisions") || $account->hasPermission('administer news entities')));

    $rows = [];

    $vids = $entity_news_storage->revisionIds($entity_news);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\entity_news\EntityNewsInterface $revision */
      $revision = $entity_news_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $entity_news->getRevisionId()) {
          $link = $this->l($date, new Url('entity.entity_news.revision', ['entity_news' => $entity_news->id(), 'entity_news_revision' => $vid]));
        }
        else {
          $link = $entity_news->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.entity_news.translation_revert', ['entity_news' => $entity_news->id(), 'entity_news_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.entity_news.revision_revert', ['entity_news' => $entity_news->id(), 'entity_news_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.entity_news.revision_delete', ['entity_news' => $entity_news->id(), 'entity_news_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['entity_news_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
