<?php


namespace Drupal\site_grabber;


trait ConfigTrait {

  public static function getFieldReferenceValue($entity, $field_name, $offset = 0) {
    /* @var $field \Drupal\Core\Field\FieldItemBase */
    /* @var $entity \Drupal\import_info\Entity\ImportContentEntity */
    try {
      $field = $entity->get($field_name);

      if (isset($offset)) {
        $value = $field->offsetGet($offset);
        return $value->get('entity')->getTarget()->getValue();
      }
      else {
        $values = [];
        foreach ($field as $item) {
          $value = $item->getValue();
          $values[] = $value->get('entity')->getTarget()->getValue();
        }
        return $values;
      }
    } catch (\Exception |\Error $err) {
      $n = 0;
    }
  }

  public static function getFieldValue($entity, $field_name, $column = NULL, $offset = 0) {
    /* @var $field \Drupal\Core\Field\FieldItemBase */
    /* @var $entity \Drupal\import_info\Entity\ImportContentEntity */
    try {
      $field = $entity->get($field_name);

      if (isset($offset)) {
        $value = $field->offsetGet($offset)->getValue();
        return $value[$column];
      }
      else {
        $values = [];
        foreach ($field as $item) {
          $value = $item->getValue();
          $values[] = $value[$column];
        }
        return $values;
      }
    } catch (\Exception |\Error $err) {
      $n = 0;
    }
  }

}