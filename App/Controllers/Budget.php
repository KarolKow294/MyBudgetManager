<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Budget extends Authenticated {
    public function indexAction() {
        View::renderTemplate('Budget/index.html');
    }

    public function incomeAction() {
        View::renderTemplate('Budget/income.html');
    }

    public function expenseAction() {
        View::renderTemplate('Budget/expense.html');
    }

    public function currentMonthAction() {
        View::renderTemplate('Budget/currentMonth.html');
    }
}
