<?php

namespace Drupal\entity_tree\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\entity_tree\Entity\EntityTreeInterface;

/**
 * Class EntityTreeController.
 *
 *  Returns responses for Entity tree routes.
 */
class EntityTreeController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Entity tree  revision.
   *
   * @param int $entity_tree_revision
   *   The Entity tree  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($entity_tree_revision) {
    $entity_tree = $this->entityManager()->getStorage('entity_tree')->loadRevision($entity_tree_revision);
    $view_builder = $this->entityManager()->getViewBuilder('entity_tree');

    return $view_builder->view($entity_tree);
  }

  /**
   * Page title callback for a Entity tree  revision.
   *
   * @param int $entity_tree_revision
   *   The Entity tree  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($entity_tree_revision) {
    $entity_tree = $this->entityManager()->getStorage('entity_tree')->loadRevision($entity_tree_revision);
    return $this->t('Revision of %title from %date', ['%title' => $entity_tree->label(), '%date' => format_date($entity_tree->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Entity tree .
   *
   * @param \Drupal\entity_tree\Entity\EntityTreeInterface $entity_tree
   *   A Entity tree  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EntityTreeInterface $entity_tree) {
    $account = $this->currentUser();
    $langcode = $entity_tree->language()->getId();
    $langname = $entity_tree->language()->getName();
    $languages = $entity_tree->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $entity_tree_storage = $this->entityManager()->getStorage('entity_tree');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity_tree->label()]) : $this->t('Revisions for %title', ['%title' => $entity_tree->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all entity tree revisions") || $account->hasPermission('administer entity tree entities')));
    $delete_permission = (($account->hasPermission("delete all entity tree revisions") || $account->hasPermission('administer entity tree entities')));

    $rows = [];

    $vids = $entity_tree_storage->revisionIds($entity_tree);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\entity_tree\EntityTreeInterface $revision */
      $revision = $entity_tree_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $entity_tree->getRevisionId()) {
          $link = $this->l($date, new Url('entity.entity_tree.revision', ['entity_tree' => $entity_tree->id(), 'entity_tree_revision' => $vid]));
        }
        else {
          $link = $entity_tree->link($date);
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
              Url::fromRoute('entity.entity_tree.translation_revert', ['entity_tree' => $entity_tree->id(), 'entity_tree_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.entity_tree.revision_revert', ['entity_tree' => $entity_tree->id(), 'entity_tree_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.entity_tree.revision_delete', ['entity_tree' => $entity_tree->id(), 'entity_tree_revision' => $vid]),
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

    $build['entity_tree_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
