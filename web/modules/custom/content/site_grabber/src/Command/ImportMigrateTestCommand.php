<?php

namespace Drupal\site_grabber\Command;

use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Console\Core\Command\Command;
use Drupal\views\Views;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function simplexml_load_file;

/**
 * Class ImportMigrateTestCommand.
 *
 * @DrupalCommand (
 *     extension="site_grabber",
 *     extensionType="module"
 * )
 */
class ImportMigrateTestCommand extends Command {


  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->getIo()->info('TestStart');
    $this->test();
  }

  protected function test() {
    $viewName = 'import_process_news';
    $viewDisplay = 'default';
    $filters = [
      'state' => 'link',
      'status' => 'start',
    ];
    $result = $this->getViewResult($viewName, $viewDisplay, $filters);
    $n = 0;
  }

  protected function getViewResult($viewName, $viewDisplay, $filters) {
    // Remove $name and $display_id from the arguments.
    $args = [];
    $view = Views::getView($viewName);
    if (is_object($view)) {
      if (is_array($args)) {
        $view->setArguments($args);
      }

      $view->setDisplay($viewDisplay);

      $views_filters = $view->display_handler->getOption('filters');
      foreach ($filters as $name => $value) {
        $filter_name = "field_{$name}_value";
        $views_filters[$filter_name]['value'] = [$value => $value];
      }
      $view->display_handler->setOption('filters', $views_filters);
      $view->preExecute();
      $view->execute();
      return $view->result;
    }
    else {
      return [];
    }
  }

  protected function getTestXml() {
    //    $file = file_get_contents('./test.xml', FILE_USE_INCLUDE_PATH);
    return simplexml_load_file('modules/custom/content/site_grabber/src/Command/test.xml');
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('imigrate:test')
      ->setDescription($this->trans('imigrate.test.description'))
      ->setAliases(['imt']);

  }

}
