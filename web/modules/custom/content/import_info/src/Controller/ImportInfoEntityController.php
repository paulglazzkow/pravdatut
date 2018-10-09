<?php

namespace Drupal\import_info\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\import_info\Entity\ImportInfoEntityInterface;

/**
 * Class ImportInfoEntityController.
 *
 *  Returns responses for Import info entity routes.
 */
class ImportInfoEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Import info entity  revision.
   *
   * @param int $import_info_entity_revision
   *   The Import info entity  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($import_info_entity_revision) {
    $import_info_entity = $this->entityManager()->getStorage('import_info_entity')->loadRevision($import_info_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('import_info_entity');

    return $view_builder->view($import_info_entity);
  }

  /**
   * Page title callback for a Import info entity  revision.
   *
   * @param int $import_info_entity_revision
   *   The Import info entity  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($import_info_entity_revision) {
    $import_info_entity = $this->entityManager()->getStorage('import_info_entity')->loadRevision($import_info_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $import_info_entity->label(), '%date' => format_date($import_info_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Import info entity .
   *
   * @param \Drupal\import_info\Entity\ImportInfoEntityInterface $import_info_entity
   *   A Import info entity  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ImportInfoEntityInterface $import_info_entity) {
    $account = $this->currentUser();
    $langcode = $import_info_entity->language()->getId();
    $langname = $import_info_entity->language()->getName();
    $languages = $import_info_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $import_info_entity_storage = $this->entityManager()->getStorage('import_info_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $import_info_entity->label()]) : $this->t('Revisions for %title', ['%title' => $import_info_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all import info entity revisions") || $account->hasPermission('administer import info entity entities')));
    $delete_permission = (($account->hasPermission("delete all import info entity revisions") || $account->hasPermission('administer import info entity entities')));

    $rows = [];

    $vids = $import_info_entity_storage->revisionIds($import_info_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\import_info\ImportInfoEntityInterface $revision */
      $revision = $import_info_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $import_info_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.import_info_entity.revision', ['import_info_entity' => $import_info_entity->id(), 'import_info_entity_revision' => $vid]));
        }
        else {
          $link = $import_info_entity->link($date);
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
              'url' => Url::fromRoute('entity.import_info_entity.revision_revert', ['import_info_entity' => $import_info_entity->id(), 'import_info_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.import_info_entity.revision_delete', ['import_info_entity' => $import_info_entity->id(), 'import_info_entity_revision' => $vid]),
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

    $build['import_info_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
