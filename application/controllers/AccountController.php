<?php

namespace application\controllers;

use application\core\Controller;

class AccountController extends Controller
{

    public function logineAction()
    {

        if (!empty($_POST)) {
            $this->model->checkPasswordExists($_POST);
            if (!$this->model->login($_POST)) {
                $this->view->message('error', $this->model->error);
            }
            $this->view->message('success', $this->model->okay);
        }


        $this->view->render('Вход');
    }

    public function loginAction()
    {
        if (!empty($_POST)) {
            if (!$this->model->validateForm(['login', 'password'], $_POST)) {

                $this->view->message('error', $this->model->error);

            } elseif (!$this->model->checkData($_POST['login'], $_POST['password'])) {
                $this->view->message('error', 'login or pass uncorrected');

            } elseif (!$this->model->checkStatus('login', $_POST['login'])) {
                $this->view->message('error', $this->model->error);
            }
            $this->model->login($_POST['login']);
            $this->view->location('/account/profile');
        }


        $this->view->render('Вход');
    }


    public function registerAction()
    {
        if (!empty($_POST)) {
            if (!$this->model->validateForm(['email', 'login', 'password'], $_POST)) {

                $this->view->message('error', $this->model->error);

            } elseif (!$this->model->checkEmailExists($_POST['email'])) {
                $this->view->message('error', $this->model->error);
            } elseif (!$this->model->checkLoginExists($_POST['login'])) {
                $this->view->message('error', $this->model->error);
            }
            $this->model->register($_POST);
            $this->view->message('success', 'ok');
        }


        $this->view->render('Регистрация');
    }

    public function recoveryAction()
    {
        $this->view->render('Восстановление пароля');
    }

    public function confirmAction()
    {
        if (!$this->model->checkTokenExists($this->route['token'])) {
            $this->view->redirect('/account/login');
        }
        $this->model->activateAccount($this->route['token']);

        $this->view->render('Регистрация завершена');
    }

    public function profileAction()
    {
        $this->view->render('Мой профиль');
    }

}