<?php

namespace Drupal\entity_tree\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the Entity tree entity.
 *
 * @ingroup entity_tree
 *
 * @ContentEntityType(
 *   id = "entity_tree",
 *   label = @Translation("Entity tree"),
 *   bundle_label = @Translation("Entity tree type"),
 *   handlers = {
 *     "storage" = "Drupal\entity_tree\EntityTreeStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\entity_tree\EntityTreeListBuilder",
 *     "views_data" = "Drupal\entity_tree\Entity\EntityTreeViewsData",
 *     "translation" = "Drupal\entity_tree\EntityTreeTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\entity_tree\Form\EntityTreeForm",
 *       "add" = "Drupal\entity_tree\Form\EntityTreeForm",
 *       "edit" = "Drupal\entity_tree\Form\EntityTreeForm",
 *       "delete" = "Drupal\entity_tree\Form\EntityTreeDeleteForm",
 *     },
 *     "access" = "Drupal\entity_tree\EntityTreeAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\entity_tree\EntityTreeHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "entity_tree",
 *   data_table = "entity_tree_field_data",
 *   revision_table = "entity_tree_revision",
 *   revision_data_table = "entity_tree_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer entity tree entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/entity_tree/{entity_tree}",
 *     "add-page" = "/admin/structure/entity_tree/add",
 *     "add-form" = "/admin/structure/entity_tree/add/{entity_tree_type}",
 *     "edit-form" = "/admin/structure/entity_tree/{entity_tree}/edit",
 *     "delete-form" = "/admin/structure/entity_tree/{entity_tree}/delete",
 *     "version-history" = "/admin/structure/entity_tree/{entity_tree}/revisions",
 *     "revision" = "/admin/structure/entity_tree/{entity_tree}/revisions/{entity_tree_revision}/view",
 *     "revision_revert" = "/admin/structure/entity_tree/{entity_tree}/revisions/{entity_tree_revision}/revert",
 *     "revision_delete" = "/admin/structure/entity_tree/{entity_tree}/revisions/{entity_tree_revision}/delete",
 *     "translation_revert" =
 *   "/admin/structure/entity_tree/{entity_tree}/revisions/{entity_tree_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/entity_tree",
 *   },
 *   bundle_entity_type = "entity_tree_type",
 *   field_ui_base_route = "entity.entity_tree_type.edit_form"
 * )
 */
class EntityTree extends RevisionableContentEntityBase implements EntityTreeInterface {

  use EntityChangedTrait;

  const LEVELS_COUNT = 10;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the entity_tree owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  private static function _addParentField(&$fields, $index) {

    $fields["type_{$index}"] = BaseFieldDefinition::create('string')
      ->setLabel(t("%index Parent type", ['%index' => $index]))
      ->setReadOnly(TRUE);

    $fields["id_{$index}"] = BaseFieldDefinition::create('integer')
      ->setLabel(t("%index Parent ID", ['%index' => $index]))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);
  }

  private static function _addParentFields(&$fields) {
    for ($index = 0; $index < self::LEVELS_COUNT; $index++) {
      self::_addParentField($fields, $index);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    self::_addParentFields($fields);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Entity tree entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Entity tree entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Entity tree is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
