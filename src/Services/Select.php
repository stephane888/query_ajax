<?php

namespace Drupal\query_ajax\Services;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\DatabaseException;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Component\Serialization\Json;
use Drupal\ajax_status\Services\Status;

class Select {

    protected $request;
    protected $Connection;
    public $AjaxStatus;

    function __construct(Connection $Connection, RequestStack $RequestStack, Status $Status) {
        $this->Connection = $Connection;
        $this->request = $RequestStack->getCurrentRequest();
        $this->AjaxStatus = $Status;
    }

    function select() {
        $results = [];
        try {
            $param = $this->request->getContent();
            $results = $this->getDatas($param);
            $this->AjaxStatus->Codes->setCode(200);
            $this->AjaxStatus->Messages->setMessage("Chargement des données ...");
        }
        catch (DatabaseException $e) {
            $this->AjaxStatus->Codes->setCode(405);
            $this->AjaxStatus->Messages->setMessage($e->getMessage());
            $results = [
              'status' => false,
              'message' => $e->getMessage(),
              'trace' => $e->getTrace()
            ];
        }
        catch (\Error $e) {
            $this->AjaxStatus->Codes->setCode(405);
            $this->AjaxStatus->Messages->setMessage($e->getMessage());
            $results = [
              'status' => false,
              'message' => $e->getMessage(),
              'trace' => $e->getTrace()
            ];
        }
        return $results;
    }

    protected function getDatas(string $param) {
        if (strpos($param, 'select') !== false) {
            return $this->Connection->query($param)
                    ->fetchAll(\PDO::FETCH_ASSOC);
        }
        else {
            throw new \Error(" Erreur dans la requette de selection de données, la requette doit contenir select ");
        }
    }

}
