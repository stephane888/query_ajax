<?php
namespace Drupal\query_ajax\Services;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\DatabaseException;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Component\Serialization\Json;

class Select {

  protected $request;

  protected $Connection;

  function __construct(Connection $Connection, RequestStack $RequestStack)
  {
    $this->Connection = $Connection;
    $this->request = $RequestStack->getCurrentRequest();
  }

  function select()
  {
    $results = [];
    try {
      $param = $this->request->getContent();
      $results = $this->getDatas($param);
    } catch (DatabaseException $e) {
      $results = [
        'status' => false,
        'message' => $e->getMessage(),
        'trace' => $e->getTrace()
      ];
    } catch (\Error $e) {
      $results = [
        'status' => false,
        'message' => $e->getMessage(),
        'trace' => $e->getTrace()
      ];
    }
    return $results;
  }

  protected function getDatas(string $param)
  {
    if (strpos($param, 'select')) {
      return $this->Connection->query($param)->fetchAll(\PDO::FETCH_ASSOC);
    } else {
      throw new \Error(" Erreur dans la requette. ");
    }
  }
}