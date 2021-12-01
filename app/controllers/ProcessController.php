<?php

class ProcessController extends ControllerBase
{
  
  public function initialize() {
    $this->tag->setTitle("Procesos");
    parent::initialize();
  }

  public function indexAction()
    {

    }

    public function importAction($idContactlist)
    {
        try {

            $contactlist = Contactlist::findFirst(array(
                "conditions" => "idContactlist = ?0",
                "bind" => array(0 => $idContactlist)
            ));

            if (!$contactlist) {
                throw new InvalidArgumentException("No se encontró la lista de contactos.");
            }

            $currentPage = $this->request->getQuery('page', null, 1);

            $builder = $this->modelsManager->createBuilder()
                ->columns(array('Importfile.idContactlist', 'User.email', 'Importcontactfile.idImportcontactfile',
                    'Importcontactfile.created', 'Importcontactfile.rows', 'Importcontactfile.imported', 'Importcontactfile.repeated',
                    'Importcontactfile.status'))
                ->from('Importfile')
                ->join('Contactlist', 'Importfile.idContactlist = Contactlist.idContactlist')
                ->join('User', 'User.idUser = Importfile.idUser')
                ->join('Importcontactfile', 'Importcontactfile.idImportfile = Importfile.idImportfile')
                ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)
                ->where("Importfile.idContactlist = {$idContactlist}");

            $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
                "builder" => $builder,
                "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
//                "limit" => 1,
                "page" => $currentPage
            ));

            $page = $paginator->getPaginate();

            $this->view->setVar("page", $page);
            $this->view->setVar("contactlist", $contactlist);
            $this->view->setVar("app_name", "process");
        } catch (InvalidArgumentException $ex) {
            $this->notification->error($ex->getMessage());
            return $this->response->redirect("contactlist/show/");
        }
    }

    public function importdetailAction($idImportcontactfile)
    {
        $importcontactfile = \Importcontactfile::findFirst(array(
            "conditions" => "idImportcontactfile = ?0",
            "bind" => array(0 => $idImportcontactfile)
        ));
        $importcontactfile->Importfile->Contactlist->created=date('d/m/Y H:ia',time());
        
        if (!$importcontactfile) {
            throw new InvalidArgumentException("No se encontró el proceso a importar.");
        }

        $this->view->setVar('importcontactfile', $importcontactfile);
        $this->view->setVar("app_name", "process");
    }

    public function getstatusAction($idImportcontactfile)
    {
        $importcontactfile = \Importcontactfile::findFirst(array(
            "conditions" => "idImportcontactfile = ?0",
            "bind" => array(0 => $idImportcontactfile)
        ));

        if (!$importcontactfile) {
            throw new InvalidArgumentException("No se encontró el proceso a importar.");
        }
        $importcontactfile = json_encode($importcontactfile);

        return $this->set_json_response($importcontactfile, 200);
    }

    public function downloaderrorAction($idImportcontactfile)
    {
        $this->view->disable();

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=ContactosNoImportados.csv');
        header('Pragma: public');
        header('Expires: 0');
        header('Content-Type: application/download');
        echo "Contactos No Importados" . PHP_EOL;
        $route = __DIR__ . "/../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . "/../errors/errors{$idImportcontactfile}.csv";
        readfile($route);
    }

    public function downloadsuccessAction($idImportcontactfile)
    {
        $this->view->disable();

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=ContactosImportados.csv');
        header('Pragma: public');
        header('Expires: 0');
        header('Content-Type: application/download');
        echo "Contactos Importados" . PHP_EOL;
        $route = __DIR__ . "/../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . "/../success/success{$idImportcontactfile}.csv";
        readfile($route);
    }

    public function startservernodeAction()
    {
      $dirServer ="bash " . __DIR__ . "/../bgprocesses/startNode.bash";

      //shell_exec("forever start " . $dirServer);
      //exec("forever start " . $dirServer, $output);
      //pcntl_exec("/usr/local/lib/node_modules/forever/bin/forever", array("forever start " . $dirServer));
      var_dump(shell_exec("(nohup " . $dirServer . " -m -r > /dev/null 2>&1)&"));
      exit;
    }

    public function restartservernodeAction()
    {

    }

    public function stopservernodeAction()
    {
      var_dump(exec("forever stopall"));
      exit;
    }
}
