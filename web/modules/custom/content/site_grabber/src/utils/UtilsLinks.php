<?php

namespace Drupal\site_grabber\utils;

use function array_shift;
use function array_unshift;
use function call_user_func_array;
use function join;
use function pathinfo;
use function preg_split;

class UtilsLinks {


  public static function createFilePath($url) {
    $path_info = pathinfo($url);
    if (!isset($path_info['extension']) || empty($path_info['extension'])) {
      return;
    }

    $filename = $path_info['basename'];

    $parts = parse_url($path_info['dirname']);
    $filepath = [];
    $path = array_filter(preg_split('/\//', $parts['path']));

    if (isset($parts['host'])) {
      $filepath[] = $parts['host'];
    }
    else {
      $filepath[] = array_shift($path);
    }


    $filepath[] = join('-', $path);
    $filepath[] = $filename;
    return join('/', $filepath);
  }

  /**
   * {@inheritdoc}
   */
  public static function addDomain($domain, $uri) {
    $url = $domain . '/' . trim($uri, "\/");
    return $url;
  }

  public static function getDomain($link_with_domain) {

    $parts = parse_url($link_with_domain);

    $url = $parts['scheme'] ? $parts['scheme'] : 'http';
    $url .= '://';
    $url .= $parts['host'];

    return $url;
  }

  /**
   * {@inheritdoc}
   */
  public static function emptyDomain($link) {
    $parts = parse_url($link);
    return !isset($parts['host']) || empty($parts['host']);
  }

  //
  //  public static function replaceElementAttribute($attribute, $callback) {
  //    return function ($element) use ($attribute, $callback) {
  //      $element->$attribute = call_user_func($callback, $element->$attribute);
  //    };
  //  }

  /**
   * {@inheritdoc}
   */
  public static function replaceAttribute($elements, $attribute, $callback, $args = []) {
    foreach ($elements as $element) {
      array_unshift($args, $element->$attribute);
      $element->$attribute = call_user_func_array($callback, $args);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function prepareExternalLink($url, $domain) {
    if (static::emptyDomain($url)) {
      return static::addDomain($domain, $url);
    }
    return $url;
  }

  public static function replaceElementsAttributes($elements, $attribute, $callback, $args = []) {

    foreach ($elements as $item) {
      $callback_args = $args;
      //      $attribute = $item[$attribute];
      /** @var \SimpleXMLElement $item */
      array_unshift($callback_args, (string) $item[$attribute][0]);
      $item[$attribute] = call_user_func_array($callback, $callback_args);
      $n = 0;
    }
  }

  public static function externalFileToLocal($xml_data) {

    $elements = [
      ['tag' => 'img', 'attribute' => 'src'],
    ];

    libxml_clear_errors();

    foreach ($elements as $element) {
      $xpath = '//' . $element['tag'];
      $elements = $xml_data->xpath($xpath);
      $attribute = $element['attribute'];
      static::replaceElementsAttributes($elements, $attribute, [self::class, 'createFilePath']);
    }
  }

  public static function prepareExternalLinks($xml_data, $domain) {

    $elements = [
      ['tag' => 'a', 'attribute' => 'href'],
      ['tag' => 'img', 'attribute' => 'src'],
    ];

    libxml_clear_errors();

    foreach ($elements as $element) {
      $xpath = '//' . $element['tag'];
      $element_xml = $xml_data->xpath($xpath);
      if (!empty($element_xml)) {
        $attribute = $element['attribute'];
        static::replaceElementsAttributes($element_xml, $attribute, [self::class, 'prepareExternalLink'], [$domain]);
      }
    }
  }
}