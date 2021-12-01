<?php

/**
 * @RoutePrefix("/api/v1")
 */
class ApiversiononeController extends \ControllerBase {

  /**
   * @Route("/echo", methods={"GET", "POST", "PUT"})
   */
  public function echoAction()
  {
    return $this->set_json_response(array('success' => 'true','method' => $this->request->getMethod(), 'response' => $this->request->getRawBody()));
  }
}