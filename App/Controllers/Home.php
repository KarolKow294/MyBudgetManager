<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Home extends \Core\Controller {

    public function before() {
    
    }

    public function after() {

    }

    public function indexAction() {
      View::renderTemplate('Home/index.html');
    }
}
