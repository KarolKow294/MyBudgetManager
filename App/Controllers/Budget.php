<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Balance;

class Budget extends Authenticated {
    private $user;

    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
    }
    
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
        $incomes = Balance::fetchIncomesByDate($this->user->id);
        $total_incomes = Balance::fetchTotalIncomesByCategoryAndDate($this->user->id);

        $sum_of_incomes = $this->sumOfAmounts($total_incomes);

        View::renderTemplate('Budget/currentMonth.html', ['incomes' => $incomes, 'total_incomes' => $total_incomes, 'sum_of_incomes' => $sum_of_incomes]);
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

    private function sumOfAmounts($total_incomes) {
        $sum = 0;

        foreach ($total_incomes as $total_income_category) {
            $sum += $total_income_category['total_income'];
        }
        return $sum;
    }
}
