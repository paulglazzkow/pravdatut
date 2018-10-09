<?php

namespace Drupal\pmtasks\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\pmtasks\Entity\PMTaskInterface;

/**
 * Class PMTaskController.
 *
 *  Returns responses for Project task routes.
 */
class PMTaskController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Project task  revision.
   *
   * @param int $pmtask_revision
   *   The Project task  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($pmtask_revision) {
    $pmtask = $this->entityManager()->getStorage('pmtask')->loadRevision($pmtask_revision);
    $view_builder = $this->entityManager()->getViewBuilder('pmtask');

    return $view_builder->view($pmtask);
  }

  /**
   * Page title callback for a Project task  revision.
   *
   * @param int $pmtask_revision
   *   The Project task  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($pmtask_revision) {
    $pmtask = $this->entityManager()->getStorage('pmtask')->loadRevision($pmtask_revision);
    return $this->t('Revision of %title from %date', ['%title' => $pmtask->label(), '%date' => format_date($pmtask->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Project task .
   *
   * @param \Drupal\pmtasks\Entity\PMTaskInterface $pmtask
   *   A Project task  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(PMTaskInterface $pmtask) {
    $account = $this->currentUser();
    $langcode = $pmtask->language()->getId();
    $langname = $pmtask->language()->getName();
    $languages = $pmtask->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $pmtask_storage = $this->entityManager()->getStorage('pmtask');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $pmtask->label()]) : $this->t('Revisions for %title', ['%title' => $pmtask->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all project task revisions") || $account->hasPermission('administer project task entities')));
    $delete_permission = (($account->hasPermission("delete all project task revisions") || $account->hasPermission('administer project task entities')));

    $rows = [];

    $vids = $pmtask_storage->revisionIds($pmtask);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\pmtasks\PMTaskInterface $revision */
      $revision = $pmtask_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $pmtask->getRevisionId()) {
          $link = $this->l($date, new Url('entity.pmtask.revision', ['pmtask' => $pmtask->id(), 'pmtask_revision' => $vid]));
        }
        else {
          $link = $pmtask->link($date);
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
              Url::fromRoute('entity.pmtask.translation_revert', ['pmtask' => $pmtask->id(), 'pmtask_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.pmtask.revision_revert', ['pmtask' => $pmtask->id(), 'pmtask_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.pmtask.revision_delete', ['pmtask' => $pmtask->id(), 'pmtask_revision' => $vid]),
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

    $build['pmtask_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
