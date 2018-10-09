<?php


namespace Drupal\site_grabber;


use Drupal\migrate\Exception\RequirementsException;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Plugin\migrate\id_map\Sql;
use Drupal\migrate\Plugin\MigrateDestinationInterface;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrateSourceInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\SourcePluginExtension;
use Drupal\site_grabber\exceptions\ImportDataException;
use Drupal\site_grabber\Plugin\migrate\source\ImportFromSite;
use Drupal\site_grabber\Plugin\migrate_plus\data_parser\ImportParserException;

class MigrateExecutableConsole extends MigrateExecutable {

  const IMPORT_NEXT = 1;

  public function setStatus($status) {
    $this->migration->setStatus($status);
  }

  protected function messageDisplay($message, $context = [], $type = 'status') {
    $this->message->display($this->t($message, $context), $type);
  }

  private function s00_CheckRequirements() {
    try {
      $this->migration->checkRequirements();
    } catch (RequirementsException $e) {
      $this->messageDisplay(
        'Migration @id did not meet the requirements. @message @requirements',
        [
          '@id' => $this->migration->id(),
          '@message' => $e->getMessage(),
          '@requirements' => $e->getRequirementsString(),
        ]
        ,
        'error'
      );
      return MigrationInterface::RESULT_FAILED;
    }
  }

  private function e1_importRewind($e) {
    $this->messageDisplay(
      'Migration failed with source plugin exception: @e', ['@e' => $e->getMessage()], 'error');
    $this->migration->setStatus(MigrationInterface::STATUS_IDLE);
    return MigrationInterface::RESULT_FAILED;
  }

  private function e2_checkImportData() {
    $this->messageDisplay(
      'Data for import not found', [], 'error');
    $this->migration->setStatus(MigrationInterface::STATUS_IDLE);
    return MigrationInterface::RESULT_FAILED;
  }


  private function s10_Rewind(SourcePluginExtension $source) {
    try {
      $source->rewind();
    } catch (ImportDataException $e) {
      return $this->e1_importRewind($e);
    } catch (ImportParserException $e) {
      return $this->e1_importRewind($e);
    } catch (\Exception $e) {
      return $this->e1_importRewind($e);
    }
  }

  private function s15_checkImportData(SourcePluginExtension $source) {
    if (FALSE === $source->hasImportData()) {
      return $this->e2_checkImportData();
    }
  }

  private function s20_ProcessRow(Row $row, $id_map) {
    try {
      $this->processRow($row);
    } catch (MigrateException $e) {
      $this->migration->getIdMap()->saveIdMapping($row, [], $e->getStatus());
      $this->saveMessage($e->getMessage(), $e->getLevel());
    } catch (MigrateSkipRowException $e) {
      if ($e->getSaveToMap()) {
        $id_map->saveIdMapping($row, [], MigrateIdMapInterface::STATUS_IGNORED);
      }
      if ($message = trim($e->getMessage())) {
        $this->saveMessage($message, MigrationInterface::MESSAGE_INFORMATIONAL);
      }
      return MigrateIdMapInterface::STATUS_IGNORED;
    }

  }

  private function s25_SaveBefore(Row $row, MigrateDestinationInterface $destination, Sql $id_map) {
    try {
      $empty_fields = $row->getEmptyDestinationProperties();
      foreach ($empty_fields as $property) {
        $source = $row->getSource();
        $this->messageDisplay('Empty value @field from @url', ['@field' => $property, '@url' => $source['source_url']]);
      }
      if (!empty($empty_fields)) {
        $this->migration->getIdMap()->saveIdMapping($row, [], MigrateIdMapInterface::STATUS_IGNORED);
        return MigrateIdMapInterface::STATUS_IGNORED;
      }

    } catch (MigrateException $e) {
      $this->migration->getIdMap()->saveIdMapping($row, [], $e->getStatus());
      $this->saveMessage($e->getMessage(), $e->getLevel());
    } catch (\Exception $e) {
      $this->migration->getIdMap()->saveIdMapping($row, [], MigrateIdMapInterface::STATUS_FAILED);
      $this->handleException($e);
    }
  }


  private function s30_Save(Row $row, MigrateDestinationInterface $destination, Sql $id_map) {
    try {
      $destination_id_values = $destination->import($row, $id_map->lookupDestinationId($this->sourceIdValues));

      if ($destination_id_values) {
        // We do not save an idMap entry for config.
        if ($destination_id_values !== TRUE) {
          $id_map->saveIdMapping($row, $destination_id_values, $this->sourceRowStatus, $destination->rollbackAction());
        }
      }
      else {
        $id_map->saveIdMapping($row, [], MigrateIdMapInterface::STATUS_FAILED);
        if (!$id_map->messageCount()) {
          $message = $this->t('New object was not saved, no error provided');
          $this->saveMessage($message);
          $this->messageDisplay($message);
        }
        return MigrateIdMapInterface::STATUS_IGNORED;
      }
    } catch (MigrateException $e) {
      $this->migration->getIdMap()->saveIdMapping($row, [], $e->getStatus());
      $this->saveMessage($e->getMessage(), $e->getLevel());
      return MigrateIdMapInterface::STATUS_IGNORED;
    } catch (\Exception $e) {
      $this->migration->getIdMap()->saveIdMapping($row, [], MigrateIdMapInterface::STATUS_FAILED);
      $this->handleException($e);
      return MigrateIdMapInterface::STATUS_IGNORED;
    }
  }

  private function s40_Next(MigrateSourceInterface $source) {
    try {
      $source->next();
    } catch (\Exception $e) {
      $this->messageDisplay(
        'Migration failed with source plugin exception: @e',
        ['@e' => $e->getMessage()], 'error');
      $this->migration->setStatus(MigrationInterface::STATUS_IDLE);
      return MigrationInterface::RESULT_FAILED;
    }
  }

  protected function getSource() {
    if (!isset($this->source)) {
      $this->source = $this->migration->getSourcePlugin();
      $this->source->setMessage($this->message);
    }
    return $this->source;
  }

  public function import() {

    if ($error = $this->s00_CheckRequirements()) {
      return $error;
    }

    $this->messageDisplay('Requirements: OK');

    $return = MigrationInterface::RESULT_COMPLETED;
    $source = $this->getSource();

    $id_map = $this->migration->getIdMap();

    if ($error = $this->s10_Rewind($source)) {
      return $error;
    }

    $this->messageDisplay('Rewind: OK');

    if ($error = $this->s15_checkImportData($source)) {
      return $error;
    }

    $destination = $this->migration->getDestinationPlugin();
    while ($source->valid()) {
      $row = $source->current();
      $this->sourceIdValues = $row->getSourceIdValues();

      if ($error = $this->s20_ProcessRow($row, $id_map)) {
        break;
      }

      if ($error = $this->s25_SaveBefore($row, $destination, $id_map)) {
        return $error;
      }

      if ($error = $this->s30_Save($row, $destination, $id_map)) {
        break;
      }

      $this->sourceRowStatus = MigrateIdMapInterface::STATUS_IMPORTED;

      // Check for memory exhaustion.
      if (($return = $this->checkStatus()) != MigrationInterface::RESULT_COMPLETED) {
        break;
      }

      // If anyone has requested we stop, return the requested result.
      if ($this->isStopping()) {
        $return = $this->migration->getInterruptionResult();
        $this->migration->clearInterruptionResult();
        break;
      }

      if ($error = $this->s40_Next($source)) {
        return $error;
      }
    }

    $this->migration->setStatus(MigrationInterface::STATUS_IDLE);
    return $return;
  }


  private function isStopping() {
    return $this->migration->getStatus() == MigrationInterface::STATUS_STOPPING;
  }
  /**
   * {@inheritdoc}
   */
  public function processRow(Row $row, array $process = NULL, $value = NULL) {
    foreach ($this->migration->getProcessPlugins($process) as $destination => $plugins) {
      $multiple = FALSE;
      /** @var $plugin \Drupal\migrate\Plugin\MigrateProcessInterface */
      foreach ($plugins as $plugin) {
        $definition = $plugin->getPluginDefinition();
        // Many plugins expect a scalar value but the current value of the
        // pipeline might be multiple scalars (this is set by the previous
        // plugin) and in this case the current value needs to be iterated
        // and each scalar separately transformed.
        if ($multiple && !$definition['handle_multiples']) {
          $new_value = [];
          if (!is_array($value)) {
            throw new MigrateException(sprintf('Pipeline failed at %s plugin for destination %s: %s received instead of an array,', $plugin->getPluginId(), $destination, $value));
          }
          $break = FALSE;
          foreach ($value as $scalar_value) {
            try {
              $new_value[] = $plugin->transform($scalar_value, $this, $row, $destination);
            }
            catch (MigrateSkipProcessException $e) {
              $new_value[] = NULL;
              $break = TRUE;
            }
          }
          $value = $new_value;
          if ($break) {
            break;
          }
        }
        else {
          try {
            $value = $plugin->transform($value, $this, $row, $destination);
          }
          catch (MigrateSkipProcessException $e) {
            $value = NULL;
            break;
          }
          $multiple = $plugin->multiple();
        }
      }
      // Ensure all values, including nulls, are migrated.
      if ($plugins) {
        if (isset($value)) {
          $row->setDestinationProperty($destination, $value);
        }
        else {
          $row->setEmptyDestinationProperty($destination);
        }
      }
      // Reset the value.
      $value = NULL;
    }
  }

}