<?php

namespace Drupal\drucon_commands\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Console\Core\Generator\GeneratorInterface;

/**
 * Class GenEntityCommand.
 *
 * @DrupalCommand (
 *     extension="drucon_commands",
 *     extensionType="module"
 * )
 */
class GenEntityCommand extends ContainerAwareCommand {

  /**
   * Drupal\Console\Core\Generator\GeneratorInterface definition.
   *
   * @var \Drupal\Console\Core\Generator\GeneratorInterface
   */
  protected $generator;

  /**
   * Constructs a new GenEntityCommand object.
   */
  public function __construct(GeneratorInterface $drucon_commands_gen_entity_generator) {
    $this->generator = $drucon_commands_gen_entity_generator;
    parent::__construct();
  }
  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('gen:entity')
      ->setDescription($this->trans('commands.gen.entity.description'));
  }

 /**
  * {@inheritdoc}
  */
  protected function initialize(InputInterface $input, OutputInterface $output) {
    parent::initialize($input, $output);
    $this->getIo()->info('initialize');
  }

 /**
  * {@inheritdoc}
  */
  protected function interact(InputInterface $input, OutputInterface $output) {
    $this->getIo()->info('interact');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->getIo()->info('execute');
    $this->getIo()->info($this->trans('commands.gen.entity.messages.success'));
    $this->generator->generate([]);
  }
}
