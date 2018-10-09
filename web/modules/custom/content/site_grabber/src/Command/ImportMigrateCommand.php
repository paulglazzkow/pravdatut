<?php

namespace Drupal\site_grabber\Command;

use Drupal\Console\Command\Shared\DatabaseTrait;
use Drupal\Console\Command\Shared\MigrationTrait;
use Drupal\Console\Core\Command\Command;
use Drupal\migrate\MigrateMessageInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drupal\site_grabber\MigrateMessageConsole;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ImportMigrateCommand extends Command {

  use DatabaseTrait;
  use MigrationTrait;

  const COMMAND_PREFIX = 'imigrate';

  protected $migrateConnection;

  protected $defaultMigrations = [];

  protected $id_prefix;

  /** @var MigrateMessageInterface $message */
  public $message;

  /**
   * Drupal\migrate_drupal\MigrationPluginManagerInterface definition.
   *
   * @var MigrationPluginManagerInterface
   */
  protected $pluginManagerMigration;

  /**
   * Constructs a new ImportMigrateExecuteCommand object.
   */
  public function __construct(MigrationPluginManagerInterface $plugin_manager_migration) {
    $this->pluginManagerMigration = $plugin_manager_migration;
    parent::__construct();
    $this->message=$this->getIo();
  }

  abstract protected function getIdPrefix();

  abstract protected function getCommandName();

  abstract protected function getCommandAliases();

  abstract protected function executeCommand(InputInterface $input, OutputInterface $output, MigrateMessageInterface $messages);

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $messages = new MigrateMessageConsole($input, $output);
    $this->executeCommand($input, $output, $messages);
  }

  protected function getId($suffix) {
    return $this->getIdPrefix() . '.' . $suffix;
  }

  private function getCommandNameFull() {
    return self::COMMAND_PREFIX . '.' . $this->getCommandName();
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName($this->getCommandNameFull())
      ->setDescription($this->trans($this->getId('description')))
      ->addArgument('migration-ids', InputArgument::IS_ARRAY, $this->trans($this->getId('arguments.id')))
      ->addOption(
        'exclude',
        NULL,
        InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
        $this->trans($this->getId('options.exclude')),
        []
      )
      ->setAliases($this->getCommandAliases());

  }

  protected function getImportMigrationsIds(InputInterface $input) {

    $migration_ids = $input->getArgument('migration-ids');
    $exclude_ids = $input->getOption('exclude');

    // If migrations weren't provided finish execution
    if (empty($migration_ids)) {
      return [];
    }

    if (!in_array('all', $migration_ids)) {
      $migrations = $migration_ids;
    }
    else {
      $migrations = array_keys($this->getMigrations());
    }

    if (!empty($exclude_ids)) {
      // Remove exclude migration from migration script
      $migrations = array_diff($migrations, $exclude_ids);
    }

    if (count($migrations) == 0) {
      $this->getIo()->error($this->trans($this->getId('messages.no-migrations')));
      return [];
    }

    return $migrations;
  }

  /**
   * @param bool $version_tag
   * @param bool $flatList
   *
   * @return array list of migrations
   */
  protected function getMigrations($version_tag = FALSE, $flatList = FALSE, $configuration = []) {

    if (!empty($this->defaultMigrations)) {
      $keys = $this->defaultMigrations;
      $migration_plugin_configuration = array_fill_keys($keys, $configuration);
      $all_migrations = $this->pluginManagerMigration->createInstances($this->defaultMigrations, $migration_plugin_configuration);
    }
    else {
      //Get migration definitions by tag
      $migrations = array_filter(
        $this->pluginManagerMigration->getDefinitions(), function ($migration) use ($version_tag) {
        return !empty($migration['migration_tags']) && in_array($version_tag, $migration['migration_tags']);
      }
      );
      // Create an array to configure all migration plugins with same configuration
      $keys = array_keys($migrations);
      $migration_plugin_configuration = array_fill_keys($keys, $configuration);
      $all_migrations = $this->pluginManagerMigration->createInstances(array_keys($migrations), $migration_plugin_configuration);
    }


    //Create all migration instances


    $migrations = [];
    foreach ($all_migrations as $migration) {
      if ($flatList) {
        $migrations[$migration->id()] = ucwords($migration->label());
      }
      else {
        //        $migrations[$migration->id()]['tags'] = implode(', ', $migration->getMigrationTags());
        $migrations[$migration->id()]['description'] = ucwords($migration->label());
      }
    }
    return $migrations;
  }

  protected function migratesFromArguments(InputInterface $input) {
    $argument=$input->getArgument('migration-ids');
    return $argument;
  }

  protected function migratesFromDefault() {

    return $this->defaultMigrations;
  }

  protected function migratesFromInput() {
    $migrations_list = $this->getMigrations();
    $migrations_ids = [];

    while (TRUE) {
      $migration_id = $this->getIo()->choiceNoList(
        $this->trans($this->getId('questions.id')),
        array_keys($migrations_list),
        'all'
      );

      if (empty($migration_id) || $migration_id == 'all') {
        // Only add all if it's the first option
        if (empty($migrations_ids) && $migration_id == 'all') {
          $migrations_ids[] = $migration_id;
        }
        break;
      }
      else {
        $migrations_ids[] = $migration_id;
      }
    }

    //    $input->setArgument('migration-ids', $migrations_ids);
    return $migrations_ids;
  }

  protected function excludeMigrates(InputInterface $input) {
    $migrations_list = $this->getMigrations();
    $exclude_ids = $input->getOption('exclude');
    if (!$exclude_ids) {
      unset($migrations_list['all']);
      while (TRUE) {
        $exclude_id = $this->getIo()->choiceNoList(
          $this->trans($this->getId('questions.exclude-id')),
          array_keys($migrations_list),
          '',
          TRUE
        );

        if (empty($exclude_id) || is_numeric($exclude_id)) {
          break;
        }
        else {
          unset($migrations_list[$exclude_id]);
          $exclude_ids[] = $exclude_id;
        }
      }
      $input->setOption('exclude', $exclude_ids);
    }
  }

  protected function printList($message, $list) {
    $this->getIo()->info($message);
    foreach ($list as $id) {
      $this->getIo()->info("  - ".$id);
    }
    $this->getIo()->info('---');
  }

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {


    if ($migrations_ids = $this->migratesFromArguments($input)) {
      $input->setArgument('migration-ids', $migrations_ids);
      $this->printList('Execute migrations (argument):', $migrations_ids);

    };

    if (empty($migrations_ids) && $migrations_ids = $this->migratesFromDefault()) {
      $input->setArgument('migration-ids', $migrations_ids);
      $this->printList('Execute migrations (default):', $migrations_ids);
    }

    if (empty($migrations_ids) && $migrations_ids = $this->migratesFromInput($input)) {
      $input->setArgument('migration-ids', $migrations_ids);
      $this->printList('Execute migrations (input):', $migrations_ids);
    }


//    $this->excludeMigrates($input);


  }

}
