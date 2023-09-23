<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Home extends \Core\Controller {
    public function indexAction() {
      if (isset($_SESSION['user_id'])) {
        View::renderTemplate('Budget/index.html');
      } else {
        View::renderTemplate('Home/index.html');
      } 
    }
}
