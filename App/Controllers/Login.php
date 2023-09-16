<?php

namespace App\Controllers;

use \App\Models\User;
use \App\Auth;
use \Core\View;
use \App\Flash;

class Login extends \Core\Controller {
  public function newAction() {
    View::renderTemplate('Login/new.html');
  }

  public function createAction() {
    $user = User::authenticate($_POST['email'], $_POST['password']);

    $remember_me = isset($_POST['remember_me']);

    if ($user) {
      Auth::login($user, $remember_me);

      Flash::addMessage('Login successful');

      //$this->redirect(Auth::getReturnToPage());
      View::renderTemplate('Home/main.html');
    } else {
      Flash::addMessage('Login unsuccessful, please try again', Flash::WARNING);

      View::renderTemplate('Login/new.html', ['email' => $_POST['email'], 'remember_me' => $remember_me]);
    }
  }

  public function destroyAction() {
    Auth::logout();

    $this->redirect('/login/show-logout-message');
  }

  public function showLogoutMessageAction() {
    Flash::addMessage('Logout successful');

    $this->redirect('/');
  }
}