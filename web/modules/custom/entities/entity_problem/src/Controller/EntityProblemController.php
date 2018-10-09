<?php

namespace Drupal\entity_problem\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\entity_problem\Entity\EntityProblemInterface;

/**
 * Class EntityProblemController.
 *
 *  Returns responses for Entity problem routes.
 */
class EntityProblemController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Entity problem  revision.
   *
   * @param int $entity_problem_revision
   *   The Entity problem  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($entity_problem_revision) {
    $entity_problem = $this->entityManager()->getStorage('entity_problem')->loadRevision($entity_problem_revision);
    $view_builder = $this->entityManager()->getViewBuilder('entity_problem');

    return $view_builder->view($entity_problem);
  }

  /**
   * Page title callback for a Entity problem  revision.
   *
   * @param int $entity_problem_revision
   *   The Entity problem  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($entity_problem_revision) {
    $entity_problem = $this->entityManager()->getStorage('entity_problem')->loadRevision($entity_problem_revision);
    return $this->t('Revision of %title from %date', ['%title' => $entity_problem->label(), '%date' => format_date($entity_problem->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Entity problem .
   *
   * @param \Drupal\entity_problem\Entity\EntityProblemInterface $entity_problem
   *   A Entity problem  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EntityProblemInterface $entity_problem) {
    $account = $this->currentUser();
    $langcode = $entity_problem->language()->getId();
    $langname = $entity_problem->language()->getName();
    $languages = $entity_problem->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $entity_problem_storage = $this->entityManager()->getStorage('entity_problem');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity_problem->label()]) : $this->t('Revisions for %title', ['%title' => $entity_problem->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all entity problem revisions") || $account->hasPermission('administer entity problem entities')));
    $delete_permission = (($account->hasPermission("delete all entity problem revisions") || $account->hasPermission('administer entity problem entities')));

    $rows = [];

    $vids = $entity_problem_storage->revisionIds($entity_problem);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\entity_problem\EntityProblemInterface $revision */
      $revision = $entity_problem_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $entity_problem->getRevisionId()) {
          $link = $this->l($date, new Url('entity.entity_problem.revision', ['entity_problem' => $entity_problem->id(), 'entity_problem_revision' => $vid]));
        }
        else {
          $link = $entity_problem->link($date);
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
              Url::fromRoute('entity.entity_problem.translation_revert', ['entity_problem' => $entity_problem->id(), 'entity_problem_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.entity_problem.revision_revert', ['entity_problem' => $entity_problem->id(), 'entity_problem_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.entity_problem.revision_delete', ['entity_problem' => $entity_problem->id(), 'entity_problem_revision' => $vid]),
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

    $build['entity_problem_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
