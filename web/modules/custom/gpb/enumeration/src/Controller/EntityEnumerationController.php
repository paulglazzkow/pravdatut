<?php

namespace Drupal\enumeration\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\enumeration\Entity\EntityEnumerationInterface;

/**
 * Class EntityEnumerationController.
 *
 *  Returns responses for Entity enumeration routes.
 */
class EntityEnumerationController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Entity enumeration  revision.
   *
   * @param int $entity_enumeration_revision
   *   The Entity enumeration  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($entity_enumeration_revision) {
    $entity_enumeration = $this->entityManager()->getStorage('entity_enumeration')->loadRevision($entity_enumeration_revision);
    $view_builder = $this->entityManager()->getViewBuilder('entity_enumeration');

    return $view_builder->view($entity_enumeration);
  }

  /**
   * Page title callback for a Entity enumeration  revision.
   *
   * @param int $entity_enumeration_revision
   *   The Entity enumeration  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($entity_enumeration_revision) {
    $entity_enumeration = $this->entityManager()->getStorage('entity_enumeration')->loadRevision($entity_enumeration_revision);
    return $this->t('Revision of %title from %date', ['%title' => $entity_enumeration->label(), '%date' => format_date($entity_enumeration->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Entity enumeration .
   *
   * @param \Drupal\enumeration\Entity\EntityEnumerationInterface $entity_enumeration
   *   A Entity enumeration  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EntityEnumerationInterface $entity_enumeration) {
    $account = $this->currentUser();
    $langcode = $entity_enumeration->language()->getId();
    $langname = $entity_enumeration->language()->getName();
    $languages = $entity_enumeration->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $entity_enumeration_storage = $this->entityManager()->getStorage('entity_enumeration');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity_enumeration->label()]) : $this->t('Revisions for %title', ['%title' => $entity_enumeration->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all entity enumeration revisions") || $account->hasPermission('administer entity enumeration entities')));
    $delete_permission = (($account->hasPermission("delete all entity enumeration revisions") || $account->hasPermission('administer entity enumeration entities')));

    $rows = [];

    $vids = $entity_enumeration_storage->revisionIds($entity_enumeration);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\enumeration\EntityEnumerationInterface $revision */
      $revision = $entity_enumeration_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $entity_enumeration->getRevisionId()) {
          $link = $this->l($date, new Url('entity.entity_enumeration.revision', ['entity_enumeration' => $entity_enumeration->id(), 'entity_enumeration_revision' => $vid]));
        }
        else {
          $link = $entity_enumeration->link($date);
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
              Url::fromRoute('entity.entity_enumeration.translation_revert', ['entity_enumeration' => $entity_enumeration->id(), 'entity_enumeration_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.entity_enumeration.revision_revert', ['entity_enumeration' => $entity_enumeration->id(), 'entity_enumeration_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.entity_enumeration.revision_delete', ['entity_enumeration' => $entity_enumeration->id(), 'entity_enumeration_revision' => $vid]),
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

    $build['entity_enumeration_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
