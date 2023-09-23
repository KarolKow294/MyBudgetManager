<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;

class Signup extends \Core\Controller {
  public function newAction() {
    View::renderTemplate('Signup/new.html');
  }

  public function createAction() {
    $user = new User($_POST);

    if ($user->save()) {
      IncomeCategory::copyDefaultCategories($user->getId());
      ExpenseCategory::copyDefaultCategories($user->getId());
      PaymentMethod::copyDefaultMethods($user->getId());

      $user->sendActivationEmail();
      
      $this->redirect('/signup/success');
    } else {
      View::renderTemplate('Signup/new.html', ['user' => $user]);
    }   
  }

  public function successAction() {
    View::renderTemplate('Signup/success.html');
  }

  public function activateAction() {
    User::activate($this->route_params['token']);

    $this->redirect('/signup/activated');
  }

  public function activatedAction() {
    View::renderTemplate('Signup/activated.html');
  }
}