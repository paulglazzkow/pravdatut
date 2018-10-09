<?php

namespace Drupal\entity_party_programm\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Entity party programm entity.
 *
 * @ingroup entity_party_programm
 *
 * @ContentEntityType(
 *   id = "entity_party_programm",
 *   label = @Translation("Entity party programm"),
 *   bundle_label = @Translation("Entity party programm type"),
 *   handlers = {
 *     "storage" = "Drupal\entity_party_programm\EntityPartyProgrammStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\entity_party_programm\EntityPartyProgrammListBuilder",
 *     "views_data" = "Drupal\entity_party_programm\Entity\EntityPartyProgrammViewsData",
 *     "translation" = "Drupal\entity_party_programm\EntityPartyProgrammTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\entity_party_programm\Form\EntityPartyProgrammForm",
 *       "add" = "Drupal\entity_party_programm\Form\EntityPartyProgrammForm",
 *       "edit" = "Drupal\entity_party_programm\Form\EntityPartyProgrammForm",
 *       "delete" = "Drupal\entity_party_programm\Form\EntityPartyProgrammDeleteForm",
 *     },
 *     "access" = "Drupal\entity_party_programm\EntityPartyProgrammAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\entity_party_programm\EntityPartyProgrammHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "entity_party_programm",
 *   data_table = "entity_party_programm_field_data",
 *   revision_table = "entity_party_programm_revision",
 *   revision_data_table = "entity_party_programm_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer entity party programm entities",
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
 *     "canonical" = "/admin/structure/entity_party_programm/{entity_party_programm}",
 *     "add-page" = "/admin/structure/entity_party_programm/add",
 *     "add-form" = "/admin/structure/entity_party_programm/add/{entity_party_programm_type}",
 *     "edit-form" = "/admin/structure/entity_party_programm/{entity_party_programm}/edit",
 *     "delete-form" = "/admin/structure/entity_party_programm/{entity_party_programm}/delete",
 *     "version-history" = "/admin/structure/entity_party_programm/{entity_party_programm}/revisions",
 *     "revision" = "/admin/structure/entity_party_programm/{entity_party_programm}/revisions/{entity_party_programm_revision}/view",
 *     "revision_revert" = "/admin/structure/entity_party_programm/{entity_party_programm}/revisions/{entity_party_programm_revision}/revert",
 *     "revision_delete" = "/admin/structure/entity_party_programm/{entity_party_programm}/revisions/{entity_party_programm_revision}/delete",
 *     "translation_revert" = "/admin/structure/entity_party_programm/{entity_party_programm}/revisions/{entity_party_programm_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/entity_party_programm",
 *   },
 *   bundle_entity_type = "entity_party_programm_type",
 *   field_ui_base_route = "entity.entity_party_programm_type.edit_form"
 * )
 */
class EntityPartyProgramm extends RevisionableContentEntityBase implements EntityPartyProgrammInterface {

  use EntityChangedTrait;

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

    // If no revision author has been set explicitly, make the entity_party_programm owner the
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

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Entity party programm entity.'))
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
      ->setDescription(t('The name of the Entity party programm entity.'))
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
      ->setDescription(t('A boolean indicating whether the Entity party programm is published.'))
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
