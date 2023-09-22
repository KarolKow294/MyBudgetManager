<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;

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

    public function createIncomeAction() {
        $income = new Income($_POST);
    
        if ($income->save()) {
            View::renderTemplate('Budget/income.html', ['income' => $income]);
        } else {
            View::renderTemplate('Budget/income.html', ['income' => $income]);
        }
    }
}
