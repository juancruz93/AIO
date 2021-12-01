<?php

class AccountcategoryController extends ControllerBase
{
  
  public function initialize() {
    $this->tag->setTitle("Categorías de cuentas");
    parent::initialize();
  }

  public function indexAction()
  {
    $this->view->setVar('app_name', "accountcategory");
  }

  public function listAction()
  {
  }

  public function createAction()
  {
    $accountCategoryForm = new AccountcategoryForm();

    $this->view->setVar('accountCategoryForm', $accountCategoryForm);
  }

  public function editAction()
  {
    $accountCategoryForm = new AccountcategoryForm();

    $this->view->setVar('accountCategoryForm', $accountCategoryForm);
  }

}