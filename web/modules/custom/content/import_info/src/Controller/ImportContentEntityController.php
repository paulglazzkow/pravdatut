<?php

namespace Drupal\import_info\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\import_info\Entity\ImportContentEntityInterface;

/**
 * Class ImportContentEntityController.
 *
 *  Returns responses for Import Content routes.
 */
class ImportContentEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Import Content  revision.
   *
   * @param int $import_content_entity_revision
   *   The Import Content  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($import_content_entity_revision) {
    $import_content_entity = $this->entityManager()->getStorage('import_content_entity')->loadRevision($import_content_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('import_content_entity');

    return $view_builder->view($import_content_entity);
  }

  /**
   * Page title callback for a Import Content  revision.
   *
   * @param int $import_content_entity_revision
   *   The Import Content  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($import_content_entity_revision) {
    $import_content_entity = $this->entityManager()->getStorage('import_content_entity')->loadRevision($import_content_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $import_content_entity->label(), '%date' => format_date($import_content_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Import Content .
   *
   * @param \Drupal\import_info\Entity\ImportContentEntityInterface $import_content_entity
   *   A Import Content  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ImportContentEntityInterface $import_content_entity) {
    $account = $this->currentUser();
    $langcode = $import_content_entity->language()->getId();
    $langname = $import_content_entity->language()->getName();
    $languages = $import_content_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $import_content_entity_storage = $this->entityManager()->getStorage('import_content_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $import_content_entity->label()]) : $this->t('Revisions for %title', ['%title' => $import_content_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all import content revisions") || $account->hasPermission('administer import content entities')));
    $delete_permission = (($account->hasPermission("delete all import content revisions") || $account->hasPermission('administer import content entities')));

    $rows = [];

    $vids = $import_content_entity_storage->revisionIds($import_content_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\import_info\ImportContentEntityInterface $revision */
      $revision = $import_content_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $import_content_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.import_content_entity.revision', ['import_content_entity' => $import_content_entity->id(), 'import_content_entity_revision' => $vid]));
        }
        else {
          $link = $import_content_entity->link($date);
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
              Url::fromRoute('entity.import_content_entity.translation_revert', ['import_content_entity' => $import_content_entity->id(), 'import_content_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.import_content_entity.revision_revert', ['import_content_entity' => $import_content_entity->id(), 'import_content_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.import_content_entity.revision_delete', ['import_content_entity' => $import_content_entity->id(), 'import_content_entity_revision' => $vid]),
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

    $build['import_content_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
