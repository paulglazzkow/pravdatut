<?php

namespace Drupal\entity_party_programm\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface;

/**
 * Class EntityPartyProgrammController.
 *
 *  Returns responses for Entity party programm routes.
 */
class EntityPartyProgrammController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Entity party programm  revision.
   *
   * @param int $entity_party_programm_revision
   *   The Entity party programm  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($entity_party_programm_revision) {
    $entity_party_programm = $this->entityManager()->getStorage('entity_party_programm')->loadRevision($entity_party_programm_revision);
    $view_builder = $this->entityManager()->getViewBuilder('entity_party_programm');

    return $view_builder->view($entity_party_programm);
  }

  /**
   * Page title callback for a Entity party programm  revision.
   *
   * @param int $entity_party_programm_revision
   *   The Entity party programm  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($entity_party_programm_revision) {
    $entity_party_programm = $this->entityManager()->getStorage('entity_party_programm')->loadRevision($entity_party_programm_revision);
    return $this->t('Revision of %title from %date', ['%title' => $entity_party_programm->label(), '%date' => format_date($entity_party_programm->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Entity party programm .
   *
   * @param \Drupal\entity_party_programm\Entity\EntityPartyProgrammInterface $entity_party_programm
   *   A Entity party programm  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EntityPartyProgrammInterface $entity_party_programm) {
    $account = $this->currentUser();
    $langcode = $entity_party_programm->language()->getId();
    $langname = $entity_party_programm->language()->getName();
    $languages = $entity_party_programm->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $entity_party_programm_storage = $this->entityManager()->getStorage('entity_party_programm');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $entity_party_programm->label()]) : $this->t('Revisions for %title', ['%title' => $entity_party_programm->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all entity party programm revisions") || $account->hasPermission('administer entity party programm entities')));
    $delete_permission = (($account->hasPermission("delete all entity party programm revisions") || $account->hasPermission('administer entity party programm entities')));

    $rows = [];

    $vids = $entity_party_programm_storage->revisionIds($entity_party_programm);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\entity_party_programm\EntityPartyProgrammInterface $revision */
      $revision = $entity_party_programm_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $entity_party_programm->getRevisionId()) {
          $link = $this->l($date, new Url('entity.entity_party_programm.revision', ['entity_party_programm' => $entity_party_programm->id(), 'entity_party_programm_revision' => $vid]));
        }
        else {
          $link = $entity_party_programm->link($date);
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
              Url::fromRoute('entity.entity_party_programm.translation_revert', ['entity_party_programm' => $entity_party_programm->id(), 'entity_party_programm_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.entity_party_programm.revision_revert', ['entity_party_programm' => $entity_party_programm->id(), 'entity_party_programm_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.entity_party_programm.revision_delete', ['entity_party_programm' => $entity_party_programm->id(), 'entity_party_programm_revision' => $vid]),
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

    $build['entity_party_programm_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
