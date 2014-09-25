<?php

/*
 * @sam
 */

class UsuarioController extends Controller {

//put your code here
    private $usuario;

    public function __construct() {
        parent::__construct();
        $this->usuario = $this->LoadModelo("usuario");
    }

    /*
     * @funcão index
     */

    public function index($pagina = FALSE) {
        if (!$this->filtraInt($pagina)) {
            $pagina = false;
        } else {
            $pagina = (int) $pagina;
        }
        Session::nivelRestrito(array("admin"));
        $this->getBibliotecas('paginador', 'paginador');
        $paginador = new Paginador();
        $this->view->titulo = "Pagina de Usuarios";
        $this->view->link = "usuario/novo";
        $this->view->usuarios = $paginador->paginar($this->usuario->listaAll(), $pagina, 5);
        $this->view->paginacao = $paginador->getView('paginacao', 'usuario/index');


        if ($this->getInt('enviar') == 1) {
            $this->view->dados = $_POST;


            if (!$this->getSqlverifica('nome')) {
                $this->view->erro = "Porfavor Introduza um nome valido ";
                $this->view->renderizar("novo");
                exit;
            }

            if (!$this->getSqlverifica('login')) {
                $this->view->erro = "Porfavor Introduza um login valido ";
                $this->view->renderizar("novo");
                exit;
            }

//            $c = $this->usuario->verificar_usuario($this->getSqlverifica('login'));
//            if ($c) {
//                $this->view->erro = "O usuario já esta registrado.";
//                $this->view->renderizar("novo");
//                exit;
//            }

            if (!$this->getSqlverifica('nivel')) {
                $this->view->erro = "Porfavor Selecciona um nivel para o usuario ";
                $this->view->renderizar("novo");
                exit;
            }

            if (!$this->alphaNumeric('senha')) {
                $this->view->erro = "Porfavor introduza uma senha valida para o  usuario ";
                $this->view->renderizar("novo");
                exit;
            }



            $this->usuario->nome = $this->getSqlverifica('nome');
            $this->usuario->login = $this->getSqlverifica('login');
            $this->usuario->nivel = $this->getSqlverifica('nivel');
            $this->usuario->senha = Hash::getHash('md5', $this->alphaNumeric('senha'), HASH_KEY);
            $this->usuario->status = "on";

            $this->usuario->Insert($this->usuario);
//                $usuario = $this->usuario->verificar_usuario($this->getSqlverifica('login'));
//                if (!$usuario) {
//                    $this->view->erro = "Não Foi Possivel Possivel Concretizar a operção  tenta mais tarde!";
//                    $this->view->renderizar("index");
//                    exit;
//                }

            $this->view->dados = FALSE;
            $this->view->mensagem = "Registro  Efectuado com Sucesso";
            $this->view->renderizar("index");
        }

        $this->view->renderizar("index");
    }

    function novo() {
        $this->view->setJs(array("novo"));
        $this->view->setCss(array("style"));
        $this->view->footer = $this->getFooter('footer', 'index');
        $this->view->renderizar('novo');
    }

    public function editar($id) {

        Session::nivelRestrito(array("admin"));
        if (!$this->filtraInt($id)) {
            $this->redirecionar("usuario");
        }
        $this->usuario->id = $this->filtraInt($id);
        $this->view->dados = $this->usuario->listarId($this->usuario);
        $this->view->titulo = "Editar Usuario";
        $this->view->setJs(array("novo"));
        if ($this->getInt("enviar")) {
            if (!$this->getSqlverifica('nome')) {
                $this->view->erro = "Porfavor Introduza um nome valido ";
                $this->view->renderizar("editar");
                exit;
            }
            if (!$this->getSqlverifica('login')) {
                $this->view->erro = "Porfavor Introduza um login valido ";
                $this->view->renderizar("editar");
                exit;
            }
            if (!$this->getSqlverifica('nivel')) {
                $this->view->erro = "Porfavor Selecciona um nivel para o usuario ";
                $this->view->renderizar("editar");
                exit;
            }
            $this->usuario->nome = $this->getSqlverifica('nome');
            $this->usuario->login = $this->getSqlverifica('login');
            $this->usuario->nivel = $this->getSqlverifica('nivel');
            $this->usuario->id = $this->view->dados->id;

            if (isset($_POST['senha'])) {
                $this->usuario->senha = $this->alphaNumeric('senha');
            }
            if (!$this->usuario->Update($this->usuario)) {
                $this->view->erro = "Erro ao alterar dados ";
                $this->view->renderizar("editar");
                exit;
            }
            $this->view->mensagem = "Alteração feita com sucesso";
        }
        $this->view->renderizar("editar");
    }

    public function apagar($id) {
        Session::nivelRestrito(array("admin"));

        if (!$this->filtraInt($id)) {
            $this->redirecionar("usuario");
        }
        $this->usuario->id = $this->filtraInt($id);
        if (!$this->usuario->listarId($this->usuario)) {
            $this->redirecionar("usuario");
        }
        $this->usuario->Delete($this->usuario);
        $this->redirecionar("usuario");
    }

    /*
     * FIM DA CLASSE
     */
}
