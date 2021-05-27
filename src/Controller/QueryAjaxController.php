<?php

namespace Drupal\query_ajax\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Serialization\Json;
use Drupal\query_ajax\Services\InsertUpdate;
use Drupal\query_ajax\Services\Select;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Query ajax routes.
 */
class QueryAjaxController extends ControllerBase {
	protected $InsertUpdate;
	protected $Select;
	/**
	 * Builds the response.
	 */
	public function build() {
		$build ['content'] = [ 
				'#type' => 'html_tag',
				'#tag' => 'pre',
				'#value' => ``
		];

		return $build;
	}

	/**
	 *
	 * {@inheritdoc}
	 */
	public static function create(ContainerInterface $container) {
		// return new static($container->get('prestashop_rest_api.cron'), $container->get('prestashop_rest_api.build_product_to_drupal'));
		return new static ( $container->get ( 'query_ajax.insert_update' ), $container->get ( 'query_ajax.select' ) );
	}

	/**
	 *
	 * @param InsertUpdate $InsertUpdate
	 * @param Select $Select
	 */
	function __construct(InsertUpdate $InsertUpdate, Select $Select) {
		$this->InsertUpdate = $InsertUpdate;
		$this->Select = $Select;
	}

	/**
	 * permet d'ajouter et modifier les données.
	 *
	 * @param Request $Request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	function Save(Request $Request) {
		$inserts = Json::decode ( $Request->getContent () );
		$configs = $this->InsertUpdate->buildInserts ( $inserts );
		return $this->reponse ( $configs, $this->InsertUpdate->AjaxStatus->getCode (), $this->InsertUpdate->AjaxStatus->getMessage () );
	}
	/**
	 * Permet de selectionner les données à partir d'une requette.
	 *
	 * @param Request $Request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	function Select(Request $Request) {
		return $this->reponse ( $this->Select->select () );
	}
	/**
	 *
	 * @param array|string $configs
	 * @param number $code
	 * @param string $message
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	protected function reponse($configs, $code = null, $message = null) {
		if (! is_string ( $configs ))
			$configs = Json::encode ( $configs );
		$reponse = new JsonResponse ();
		if ($code)
			$reponse->setStatusCode ( $code, $message );
		$reponse->setContent ( $configs );
		return $reponse;
	}
}
