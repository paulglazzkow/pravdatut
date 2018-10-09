<?php


namespace Drupal\site_grabber;


use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\migrate\MigrateMessageInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateMessageConsole implements MigrateMessageInterface {

  protected $io;


  public function __construct(InputInterface $input, OutputInterface $output) {
    $this->io = new DrupalStyle($input, $output);
  }

  /**
   * Displays a migrate message.
   *
   * @param string $message
   *   The message to display.
   * @param string $type
   *   The type of message, for example: status or warning.
   */
  public function display($message, $type = 'status') {
    switch ($type) {
      case 'status':
        $this->io->info($message);
        break;
      case 'warning':
        $this->io->warning($message);
        break;
      case 'error':
        $this->io->error($message);
        break;

      default:
        $this->io->info($message);
    }
  }
}