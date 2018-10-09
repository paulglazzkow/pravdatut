<?php

namespace Drupal\entity_tech\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\entity_tech\Entity\EntityTechInterface;

/**
 * Class EntityTechController.
 *
 *  Returns responses for Entity tech routes.
 */
class EntityTechController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Entity tech  revision.
   *
   * @param int $entity_tech_revision
   *   The Entity tech  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($entity_tech_revision) {
    $entity_tech = $this->entityManager()->getStorage('entity_tech')->loadRevision($entity_tech_revision);
    $view_builder = $this->entityManager()->getViewBuilder('entity_tech');

    return $view_builder->view($entity_tech);
  }

  /**
   * Page title callback for a Entity tech  revision.
   *
   * @param int $entity_tech_revision
   *   The Entity tech  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($entity_tech_revision) {
    $entity_tech = $this->entityManager()->getStorage('entity_tech')->loadRevision($entity_tech_revision);
    return $this->t('Revision of %title from %date', ['%title' => $entity_tech->label(), '%date' => format_date($entity_tech->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Entity tech .
   *
   * @param \Drupal\entity_tech\Entity\EntityTechInterface $entity_tech
   *   A Entity tech  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EntityTechInterface $entity_tech) {
    $account = $this->currentUser();
    $langcode = $entity_tech->language()->getId();
    $langname = $entity_tech->language()->getName();
    $languages = $entity_tech->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $entity_tech_storage = $this->entityManager()->getStorage('entity_tech');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity_tech->label()]) : $this->t('Revisions for %title', ['%title' => $entity_tech->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all entity tech revisions") || $account->hasPermission('administer entity tech entities')));
    $delete_permission = (($account->hasPermission("delete all entity tech revisions") || $account->hasPermission('administer entity tech entities')));

    $rows = [];

    $vids = $entity_tech_storage->revisionIds($entity_tech);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\entity_tech\EntityTechInterface $revision */
      $revision = $entity_tech_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $entity_tech->getRevisionId()) {
          $link = $this->l($date, new Url('entity.entity_tech.revision', ['entity_tech' => $entity_tech->id(), 'entity_tech_revision' => $vid]));
        }
        else {
          $link = $entity_tech->link($date);
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
              'url' => Url::fromRoute('entity.entity_tech.revision_revert', ['entity_tech' => $entity_tech->id(), 'entity_tech_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.entity_tech.revision_delete', ['entity_tech' => $entity_tech->id(), 'entity_tech_revision' => $vid]),
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

    $build['entity_tech_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
