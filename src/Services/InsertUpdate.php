<?php
namespace Drupal\query_ajax\Services;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\DatabaseException;
use Drupal\ajax_status\Services\Status;

class InsertUpdate {

  protected $Connection;

  public $AjaxStatus;

  private $transastionRunning = false;

  function __construct(Connection $Connection, Status $Status)
  {
    $this->Connection = $Connection;
    $this->AjaxStatus = $Status;
  }

  /**
   * insertion multiple.
   * Pour lemoment tous les erreurs ne sont pas attrappers.
   *
   * @param array $inserts
   * @throws \Error
   * @return boolean[]
   */
  public function buildInserts(array $inserts, $colum_id_name = '', $value_id = 0)
  {
    $results = [];
    foreach ($inserts as $key => $insert) {
      try {
        if (! empty($insert['childstable']) && ! $this->transastionRunning) {
          $this->transastionRunning = true;
          $this->Connection->startTransaction();
        }

        if (! empty($insert['table']) && isset($insert['fields'])) {
          $results[$key] = [
            'table' => $insert['table'],
            'status' => true
          ];
          /**
           * Lors d'un ajout on peut ajouter les elements dans plusieurs tables avec les id de parents.
           */
          if (empty($insert['where'])) {
            if (! empty($colum_id_name)) {
              $insert['fields'][$colum_id_name] = $value_id;
            }
            $idparent = $this->insert($insert['table'], $insert['fields']);
            $results[$key]['result'] = $idparent;
            if (! empty($insert['childstable']) && ! empty($insert['childstable']['colum_id_name'])) {
              $results[$key]['childstable'] = $this->buildInserts($insert['childstable']['tables'], $insert['childstable']['colum_id_name'], $idparent);
            }
          } else {
            if (isset($insert['action'])) {
              switch ($insert['action']) {
                case 'delete':
                  if (! empty($insert['childstable'])) {
                    $results[$key]['result'] = $this->buildInserts($insert['childstable']['tables']);
                  }
                  $results[$key]['result'] = $this->delete($insert['table'], $insert['where']);
                  break;
                case 'update':
                  $results[$key]['result'] = $this->update($insert['table'], $insert['fields'], $insert['where']);
                  break;
                default:
                  throw new \Error(" La valeur action n'est pas valide. ");
                  break;
              }
            } else
              throw new \Error(" La valeur action n'est pas definie. ");
          }
        } else {
          throw new \Error(" Erreur dans la configuration de la requete: la table ou les champs ne sont pas definit ");
        }
      } catch (DatabaseException $e) {
        $this->AjaxStatus->Codes->setCode(405);
        $this->AjaxStatus->Messages->setMessage('Erreur de sauvegarde');
        if ($this->transastionRunning) {
          $this->Connection->rollBack();
        }
        $results[$key] = [
          'status' => false,
          'message' => $e->getMessage(),
          'trace' => $e->getTrace()
        ];
      } catch (\Error $e) {
        $this->AjaxStatus->Codes->setCode(405);
        $this->AjaxStatus->Messages->setMessage('Erreur de sauvegarde');
        $results[$key] = [
          'status' => false,
          'message' => $e->getMessage(),
          'trace' => $e->getTrace()
        ];
      } catch (\Exception $e) {
        $this->AjaxStatus->Codes->setCode(405);
        $this->AjaxStatus->Messages->setMessage('Erreur de sauvegarde');
        $results[$key] = [
          'status' => false,
          'message' => $e->getMessage(),
          'trace' => $e->getTrace()
        ];
      }
    }
    return $results;
  }

  protected function insert(String $table, array $fields)
  {
    return $this->Connection->insert($table)
      ->fields($fields)
      ->execute();
  }

  protected function delete(String $table, array $where)
  {
    $query = $this->Connection->delete($table);
    foreach ($where as $value) {
      if (! empty($value['column']) && ! empty($value['value'])) {
        if (! empty($value['opetator'])) {
          $query->condition($value['column'], $value['value'], $value['opetator']);
        } else {
          $query->condition($value['column'], $value['value'], '=');
        }
      } else {
        throw new \Error("Erreur dans la configuration le groupe 'where'");
      }
    }
    return $query->execute();
  }

  protected function update(String $table, array $fields, array $where)
  {
    $query = $this->Connection->update($table)->fields($fields);
    foreach ($where as $value) {
      if (! empty($value['column']) && ! empty($value['value'])) {
        if (! empty($value['opetator'])) {
          $query->condition($value['column'], $value['value'], $value['opetator']);
        } else {
          $query->condition($value['column'], $value['value'], '=');
        }
      } else {
        throw new \Error("Erreur dans la configuration le groupe 'where'");
      }
    }
    return $query->execute();
  }
}