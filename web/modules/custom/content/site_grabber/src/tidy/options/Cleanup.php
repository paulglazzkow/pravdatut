<?php

namespace Drupal\site_grabber\tidy\options;

use Drupal\site_grabber\tidy\Tidy;

class Cleanup {

  var $tidy;

  public function __construct(Tidy $tidy) {
    $this->tidy = $tidy;
  }

  /*
   bare:
  Type:	Boolean
  Default:	no
  Values:	no, yes
    #  Этот параметр указывает, должен ли Tidy лишать Microsoft специфический HTML из документов Word 2000
    #  и выводить пространства, а не неразрывные пробелы, где они существуют во вводе.
  */

  function bare($value) {
    $this->tidy->setOption('bare', $value);
    return $this;
  }

  /*
  clean:
  Type:  Boolean
  Default:  no
  Values:  no, yes
    #  Эта опция указывает, должен ли Tidy выполнять очистку некоторых устаревших презентационных тегов
    #  (в настоящее время <i>, <b>, <center>, когда они заключены в соответствующие встроенные теги и <font>).
    #  Если установлено «Да», то устаревшие теги будут заменены тегами CSS <style> и структурной разметкой,
    #  если это необходимо.
    */
  function clean($value) {
    $this->tidy->setOption('clean', $value);
    return $this;
  }

  /*
  drop-empty-elements:
  Type:  Boolean
  Default:  yes
  Values:  no, yes
    #  Эта опция указывает, должен ли Tidy удалять пустые элементы.
  */

  function drop_empty_elements($value) {
    $this->tidy->setOption('bare', $value);
    return $this;
  }

  /*
  drop-empty-paras:
  Type:  Boolean
  Default:  yes
  Values:  no, yes
    #  Этот параметр указывает, должен ли Tidy отказаться от пустых абзацев.

    */

  function drop_empty_paras($value) {
    $this->tidy->setOption('drop-empty-paras', $value);
    return $this;
  }

  /*

  drop-proprietary-attributes:
  Type:  Boolean
  Default:  no
  Values:  no, yes
    #  Этот параметр указывает, должен ли Tidy лишать проприетарные атрибуты,
    #  такие как атрибуты привязки данных Microsoft. Кроме того, атрибуты,
    #  которые не разрешены в выходной версии HTML, будут удалены при
    #  использовании со "strict-tags-attributes".
  */

  function drop_proprietary_attributes($value) {
    $this->tidy->setOption('drop-proprietary-attributes', $value);
    return $this;
  }

  /*
  gdoc:
  Type:  Boolean
  Default:  no
  Values:  no, yes
    #  Этот параметр указывает, следует ли Tidy включить определенное поведение для очистки HTML,
    #  экспортированного из Документов Google.

    */
  function gdoc($value) {
    $this->tidy->setOption('gdoc', $value);
    return $this;
  }

  /*
  logical-emphasis:
  Type:  Boolean
  Default:  no
  Values:  no, yes
    #  Эта опция указывает, следует ли Tidy заменить любое вхождение <i> на <em>
    #  и любое вхождение <b> с <strong>. Любые атрибуты сохраняются без изменений.
    #  Эта опция может быть установлена независимо от опции очистки.

    */
  function logical_emphasis($value) {
    $this->tidy->setOption('logical-emphasis', $value);
    return $this;
  }

  /*

  merge-divs:
  Type:  Enum
  Default:  auto
  Values:  no, yes, auto
  See also:  clean, merge-spans
    #  Этот параметр можно использовать для изменения поведения "clean" при установке «Да».
    #  Эта опция указывает, должен ли Tidy объединить вложенный <div>, такой как <div> <div> ... </ div> </ div>.
    #  Если установлено значение auto, атрибуты внутреннего <div> перемещаются во внешний.
    #  Вложенные <div> с атрибутами id не объединены.
    #  Если установлено yes, атрибуты внутреннего <div> отбрасываются за исключением "class" и "style".
  */

  function merge_divs($value) {
    $this->tidy->setOption('merge-divs', $value);
    return $this;
  }

  /*
  merge-spans:
  Type:  Enum
  Default:  auto
  Values:  no, yes, auto
  See also:  clean, merge-divs
    #  Этот параметр можно использовать для изменения поведения "clean" при установке «Да».
    #  Эта опция указывает, должен ли Tidy объединить вложенный <span>, такой как <span> <span> ... </ span> </ span>.
    #  Алгоритм идентичен алгоритму, используемому "merge-divs".

    */
  function merge_spans($value) {
    $this->tidy->setOption('merge-spans', $value);
    return $this;
  }

  /*
  word-2000:
  Type:  Boolean
  Default:  no
  Values:  no, yes
    #  Этот параметр указывает, должен ли Tidy прилагать большие усилия, чтобы вырезать все излишки,
    #  которые Microsoft Word 2000 вставляет, когда вы сохраняете документы Word как «веб-страницы».
    #  Он не обрабатывает встроенные изображения или VML.
    #  Вам следует рассмотреть возможность сохранения с помощью Word Save As ... и выбора веб-страницы, Filtered.
  */

  function word_2000($value) {
    $this->tidy->setOption('word-2000', $value);
    return $this;
  }

}