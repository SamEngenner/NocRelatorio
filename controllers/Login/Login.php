<?php

namespace controllers;

use application\Controller;
use application\Hash;
use application\Session;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of loginController
 *
 * @author sam
 */
class Login extends Controller {

    //put your code here

    private $log;

    public function __construct() {
        parent::__construct();
        $this->log = $this->LoadModelo('Usuarios');
    }

    public function index() {

        $this->view->setJs(array("novo"));
        $this->view->setCss(array("style"));
        $this->view->titulo = "Iniciar Sessão";
        if ($this->getInt('enviar') == 1) {
            $this->dados = $_POST;
            if (!$this->getSqlverifica('login')) {
                $this->view->erro = "POrfavor Introduza um nome Valido";
                $this->view->renderizar("index");
                exit;
            }
            if (!$this->getSqlverifica('senha')) {
                $this->view->erro = "POrfavor Introduza uma senha Valida";
                $this->view->renderizar("index");
                exit;
            }

            $this->log->setLogin($this->getSqlverifica('login'));
            $this->log->setSenha(Hash::getHash('md5', $this->alphaNumeric('senha'), HASH_KEY));

            $linha = $this->log->Autenticar($this->log);

            if (!$linha) {
                $this->view->erro = "Usuario ou Palavra Passe Incorreta";
                $this->view->renderizar("index");
                exit;
            }
            if ($linha->getStatus() != 'on') {
                $this->view->erro = "Esse Usuario Não Esta Activo";
                $this->view->renderizar("index");
                exit;
            }
           
            Session::set("autenticado", true);
            Session::set('nivel', $linha->getNivel());
            Session::set('nome', $linha->getNome());
            Session::set('id', $linha->getId());
            Session::set('time', time());

            if (Session::get('nivel') == "admin") {
                $this->redirecionar('dashboard');
            } else {
                $this->redirecionar();
            }
        }


        $this->view->renderizar("index");
    }

    public function logof() {
        Session::destruir(array('autenticado', 'nivel', 'nome', 'id', 'time'));
        $this->redirecionar("login");
    }

}
