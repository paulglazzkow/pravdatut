<?php

namespace Drupal\site_grabber\parse_settings;

use Drupal\site_grabber\ConfigTrait;
use Symfony\Component\Yaml\Yaml;

class ParseSettingsFactory {

  use ConfigTrait;

  private static function createConfigLink($config_entity, $source_url) {
    $config = self::prepareConfigYML($config_entity, 'field_link_config_yml');
    $config['source_url'] = $source_url;
    $config['source_format'] = self::getFieldValue($config_entity, 'field_link_source_format', 'value');
    return $config;
  }

  private static function createConfigContent($content_entity) {

    $config_entity = self::getFieldReferenceValue($content_entity, 'field_import_config');

    $config = self::prepareConfigYML($config_entity, 'field_content_config_yml');

    $config['entity_id'] = $content_entity->id();
    $config['source_url'] = self::getFieldValue($content_entity, 'field_source_url', 'uri');

    $config['source_format'] = self::getFieldValue($config_entity, 'field_content_source_format', 'value');
    return $config;
  }

  private static function createConfigProcessImportImage($content_entity) {

    $config_entity = self::getFieldReferenceValue($content_entity, 'field_import_config');

    $config = [
      'process' => [
        'field_source_url' => 'source_url',
        'field_media_image' => [
          'plugin' => 'image_import',
          'destination' => 'public://images_import/news/' . $content_entity->id(),
        ],
      ],
      'source'=>[
        'field_selectors'=>[
          'source_url'=>'//img/@src'
        ]
      ]
    ];

    $config['entity_id'] = $content_entity->id();
    $config['source_url'] = 'field_source_url';
    $config['source_format'] = self::getFieldValue($config_entity, 'field_link_source_format', 'value');
    return $config;
  }


  private static function addConfigCommon(&$config, $config_entity, $configuration) {

    $config['entity'] = $config_entity;

    if (isset($config['process']) && isset($configuration['process'])) {
      $config['process'] += $configuration['process'];
    }
    else {
      $config['process'] = $configuration['process'];
    }
  }


  public static function create($type, $config_entity, $destination_id, $configuration) {
    switch ($type) {
      case 'link':
        $config = self::createConfigLink($config_entity, $destination_id);
        break;
      case 'content':
        $config = self::createConfigContent($config_entity);
        break;
      case 'process_import_image':
        $config = self::createConfigProcessImportImage($config_entity);
        break;
    }

    self::addConfigCommon($config, $config_entity, $configuration);

    switch ($config['source_format']) {
      case 'html':
        $settings = new HTMLSettings($config);
        break;
      case 'json':
        $settings = new JSONSettings($config);
        break;
    }
    return $settings;
  }


  private static function createConfig($format, $config) {
    switch ($format) {
      case 'html':
        $settings = new HTMLSettings($config);
        break;
      case 'json':
        $settings = new JSONSettings($config);
        break;
    }
    return $settings;
  }

  public static function createLink($config_entity, $source_link, $configuration) {

    $config = self::createConfigLink($config_entity, $source_link);
    $config += self::addConfigCommon($source_link, $config_entity, $configuration);
    $settings = self::createConfig($config['source_format'], $config);
    return $settings;
  }

  private static function prepareConfigYML($config_entity, $field_name) {
    $value = self::getFieldValue($config_entity, $field_name, 'value');
    $yml = Yaml::parse($value);
    return $yml;
  }

}