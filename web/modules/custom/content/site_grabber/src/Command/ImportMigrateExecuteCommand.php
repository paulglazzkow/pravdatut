<?php

namespace Drupal\site_grabber\Command;

use Drupal\Console\Annotations\DrupalCommand;
use Drupal\migrate\MigrateMessageInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\site_grabber\MigrateExecutableConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportMigrateExecuteCommand.
 *
 * @DrupalCommand (
 *     extension="site_grabber",
 *     extensionType="module"
 * )
 */
class ImportMigrateExecuteCommand extends ImportMigrateCommand {

  protected $defaultMigrations = [];

  protected function getIdPrefix() {
    return 'commands.imigrate.execute';
  }

  protected function getCommandName() {
    return 'execute';
  }

  protected function getCommandAliases() {
    return ['ime'];
  }

  /**
   * {@inheritdoc}
   */
  protected function executeCommand(InputInterface $input, OutputInterface $output, MigrateMessageInterface $messages) {

    $migrations = $this->getImportMigrationsIds($input);

    foreach ($migrations as $migration_id) {
      $this->getIo()->info(
        sprintf(
          $this->trans($this->getId('messages.processing')),
          $migration_id
        )
      );

      $configuration = [];
      $migration_service = $this->pluginManagerMigration->createInstance($migration_id, $configuration);

      if ($migration_service) {

        $executable = new MigrateExecutableConsole($migration_service, $messages);
        $executable->setStatus(MigrationInterface::STATUS_IDLE);

        $this->getIo()->info('IMPORT START:' . $migration_id);
        $migration_status = $executable->import();

        switch ($migration_status) {
          case MigrationInterface::RESULT_COMPLETED:
            $this->getIo()->info(
              sprintf(
                $this->trans($this->getId('messages.imported')),
                $migration_id
              )
            );
            break;
          case MigrationInterface::RESULT_INCOMPLETE:
            $this->getIo()->info(
              sprintf(
                $this->trans($this->getId('messages.importing-incomplete')),
                $migration_id
              )
            );
            break;
          case MigrationInterface::RESULT_STOPPED:
            $this->getIo()->error(
              sprintf(
                $this->trans('commands.migrate.execute.messages.import-stopped'),
                $migration_id
              )
            );
            break;
          case MigrationInterface::RESULT_FAILED:
            $this->getIo()->error(
              sprintf(
                $this->trans('commands.migrate.execute.messages.import-fail'),
                $migration_id
              )
            );
            break;
          case MigrationInterface::RESULT_SKIPPED:
            $this->getIo()->error(
              sprintf(
                $this->trans('commands.migrate.execute.messages.import-skipped'),
                $migration_id
              )
            );
            break;
          case MigrationInterface::RESULT_DISABLED:
            // Skip silently if disabled.
            break;
        }
      }
      else {
        $this->getIo()->error($this->trans('commands.imigrate.execute.messages.fail-load'));

        return 1;
      }
    }

    return 0;
  }
}
