<?php

namespace Drupal\site_grabber\Command;

use Drupal\Console\Annotations\DrupalCommand;
use Drupal\migrate\MigrateMessageInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\site_grabber\MigrateExecutableConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportMigrateResetCommand.
 *
 * @DrupalCommand (
 *     extension="site_grabber",
 *     extensionType="module"
 * )
 */
class ImportMigrateResetCommand extends ImportMigrateCommand {

  protected $defaultMigrations = ['import_content_news'];

  protected function getCommandName() {
    return 'reset';
  }

  protected function getCommandAliases() {
    return ['imr'];
  }

  protected function getIdPrefix() {
    return 'commands.imigrate.reset';
  }

  /**
   * {@inheritdoc}
   */
  protected function executeCommand(InputInterface $input, OutputInterface $output, MigrateMessageInterface $messages) {
    $migrations = $this->getImportMigrationsIds($input);

    foreach ($migrations as $migration_id) {
      $this->getIo()->info(
        sprintf(
          $this->trans('commands.migrate.execute.messages.processing'),
          $migration_id
        )
      );

      $configuration = [];
      $migration_service = $this->pluginManagerMigration->createInstance($migration_id, $configuration);

      if ($migration_service) {
        $executable = new MigrateExecutableConsole($migration_service, $messages);
        $executable->setStatus(MigrationInterface::STATUS_IDLE);
      }
      else {
        $this->getIo()->error($this->trans('commands.imigrate.execute.messages.fail-load'));
        return 1;
      }
    }

    return 0;
  }
}
