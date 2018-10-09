<?php

namespace Drupal\entity_events\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\entity_events\Entity\EntityEventInterface;

/**
 * Class EntityEventController.
 *
 *  Returns responses for Event routes.
 */
class EntityEventController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Event  revision.
   *
   * @param int $entity_event_revision
   *   The Event  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($entity_event_revision) {
    $entity_event = $this->entityManager()->getStorage('entity_event')->loadRevision($entity_event_revision);
    $view_builder = $this->entityManager()->getViewBuilder('entity_event');

    return $view_builder->view($entity_event);
  }

  /**
   * Page title callback for a Event  revision.
   *
   * @param int $entity_event_revision
   *   The Event  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($entity_event_revision) {
    $entity_event = $this->entityManager()->getStorage('entity_event')->loadRevision($entity_event_revision);
    return $this->t('Revision of %title from %date', ['%title' => $entity_event->label(), '%date' => format_date($entity_event->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Event .
   *
   * @param \Drupal\entity_events\Entity\EntityEventInterface $entity_event
   *   A Event  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EntityEventInterface $entity_event) {
    $account = $this->currentUser();
    $langcode = $entity_event->language()->getId();
    $langname = $entity_event->language()->getName();
    $languages = $entity_event->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $entity_event_storage = $this->entityManager()->getStorage('entity_event');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity_event->label()]) : $this->t('Revisions for %title', ['%title' => $entity_event->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all event revisions") || $account->hasPermission('administer event entities')));
    $delete_permission = (($account->hasPermission("delete all event revisions") || $account->hasPermission('administer event entities')));

    $rows = [];

    $vids = $entity_event_storage->revisionIds($entity_event);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\entity_events\EntityEventInterface $revision */
      $revision = $entity_event_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $entity_event->getRevisionId()) {
          $link = $this->l($date, new Url('entity.entity_event.revision', ['entity_event' => $entity_event->id(), 'entity_event_revision' => $vid]));
        }
        else {
          $link = $entity_event->link($date);
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
              Url::fromRoute('entity.entity_event.translation_revert', ['entity_event' => $entity_event->id(), 'entity_event_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.entity_event.revision_revert', ['entity_event' => $entity_event->id(), 'entity_event_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.entity_event.revision_delete', ['entity_event' => $entity_event->id(), 'entity_event_revision' => $vid]),
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

    $build['entity_event_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
