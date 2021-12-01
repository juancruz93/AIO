<?php

namespace Sigmamovil\Wrapper;

require_once __DIR__ . "/../general/misc/forceutf8/src/ForceUTF8/Encoding.php";

class FooterWrapper extends \BaseWrapper {

  public function findFooter($page) {

    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $footer = \Footer::find(array(
                "conditions" => "idAllied = ?0 AND deleted = 0",
                "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
                "offset" => $page,
                "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
    ));
    $total = \Footer::count(array(
                "conditions" => "idAllied = ?0 AND deleted = 0",
                "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
    ));

    $consult = array();
    if (count($footer)) {
      foreach ($footer as $key => $value) {
        $consult[$key] = array(
            "idFooter" => $value->idFooter,
            "name" => $value->name,
            "description" => $value->description,
            "content" => $value->content,
        );
      }
    }

    $arrFinish = array("total" => $total, "total_pages" => ceil($total / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT), "items" => $consult);
    return $arrFinish;
    //$this->mailcategory = array("total"=>count($total),"total_pages"=>ceil(count($total) / 2),"items"=>$consult);
  }

  public function saveFooter($arrFooter) {
    $footer = new \Footer();
    $footerForm = new \FooterForm();
    $forceUtf8 = new \ForceUTF8\Encoding();
    $content = $forceUtf8->fixUTF8($arrFooter['editor']);
    $footer->idAllied = \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied;
    
    $forceUtf8 = new \ForceUTF8\Encoding();
    $footer->content = $forceUtf8->fixUTF8($arrFooter['editor']);
//    $footer->content = $arrFooter['editor'];
    $footer->deleted = 0;
    $footerForm->bind($arrFooter, $footer);

    if (!$footerForm->isValid()) {
      foreach ($footerForm->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $footer->content = $content;
    if (!$footer->save()) {
      foreach ($footer->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  public function updateFooter($arrFooter) {

    $footer = \Footer::findFirst(array(
                "conditions" => "idFooter = ?0",
                "bind" => array(0 => $arrFooter['idFooter'])
    ));

    if (!$footer) {
      throw new \InvalidArgumentException("El footer que desea editar no existe, por favor valide la información");
    }
    $footerForm = new \FooterForm();

    $forceUtf8 = new \ForceUTF8\Encoding();
    $content = $forceUtf8->fixUTF8($arrFooter['editor']);
    $footer->content = $content;
    
//    $footer->content = $arrFooter['editor'];
    $footerForm->bind($arrFooter, $footer);

    if (!$footerForm->isValid()) {
      foreach ($footerForm->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    if (!$footer->update()) {
      foreach ($footer->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

}
