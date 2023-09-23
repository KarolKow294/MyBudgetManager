<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;

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
    
        $income->save();

        View::renderTemplate('Budget/income.html', ['income' => $income]);
    }

    public function createExpenseAction() {
        $expense = new Expense($_POST);
    
        $expense->save();

        View::renderTemplate('Budget/expense.html', ['expense' => $expense]);
    }
}
