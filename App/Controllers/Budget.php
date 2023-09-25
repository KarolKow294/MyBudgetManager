<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Balance;

class Budget extends Authenticated {
    private $user;
    public $errors = [];

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
        $title = 'current month';

        $start_date = date('Y-m') . '-01';
        $end_date = date('Y-m-t');
        
        $this->sendDataToBalanceView($start_date, $end_date, $title);
    }

    public function previousMonthAction() {
        $title = 'previous month';

        $start_date = date('Y-m-d', strtotime('first day of last month'));
        $end_date = date('Y-m-d', strtotime('last day of last month'));
        
        $this->sendDataToBalanceView($start_date, $end_date, $title);
    }

    public function currentYearAction() {
        $title = 'current year';

        $start_date = date('Y') . '-01-01';
        $end_date = date('Y') . '-12-31';
        
        $this->sendDataToBalanceView($start_date, $end_date, $title);
    }

    public function selectedPeriodAction() {
        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'];

        $title = $start_date . ' ÷ ' . $end_date;
        
        $this->sendDataToBalanceView($start_date, $end_date, $title);
    }

    private function sendDataToBalanceView($start_date, $end_date, $title) {
        $this->validateDate($start_date, $end_date);

        if (empty($this->errors)) {
            
            $incomes = Balance::fetchIncomesByDate($this->user->id, $start_date, $end_date);
            $total_incomes = Balance::fetchTotalIncomesByCategoryAndDate($this->user->id, $start_date, $end_date);

            $expenses = Balance::fetchExpensesByDate($this->user->id, $start_date, $end_date);
            $total_expenses = Balance::fetchTotalExpensesByCategoryAndDate($this->user->id, $start_date, $end_date);

            $sum_of_incomes = $this->sumOfAmounts($total_incomes);
            $sum_of_expenses = $this->sumOfAmounts($total_expenses);

            $balance = $sum_of_incomes - $sum_of_expenses;

            View::renderTemplate('Budget/balance.html', ['incomes' => $incomes, 'total_incomes' => $total_incomes, 'sum_of_incomes' => $sum_of_incomes, 'expenses' => $expenses, 'total_expenses' => $total_expenses, 'sum_of_expenses' => $sum_of_expenses, 'balance' => $balance, 'title' => $title, 'date_errors' => $this->errors]);

        } else {
            View::renderTemplate('Budget/balance.html', ['title' => $title, 'date_errors' => $this->errors]);
        }
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

    private function sumOfAmounts($totals) {
        $sum = 0;

        foreach ($totals as $total) {
            $sum += $total['total_amount_by_category'];
        }
        return $sum;
    }

    private function validateDate($start_date, $end_date) {

        if ($start_date != '') {
            $date = date_create_from_format('Y-m-d', $start_date);
            
            if ($date === false) {
                $this->errors[] = 'Data startowa musi być w formacie YYYY-MM-DD';
            }
        } else {
            $this->errors[] = 'Data startowa jest wymagana';
        }

        if ($end_date != '') {
            $date = date_create_from_format('Y-m-d', $end_date);
            
            if ($date === false) {
                $this->errors[] = 'Data końcowa musi być w formacie YYYY-MM-DD';
            }
        } else {
            $this->errors[] = 'Data końcowa jest wymagana';
        }

        if (strtotime($start_date) > strtotime($end_date)) {
            $this->errors[] = 'Data końcowa jest wcześniejsza niż startowa';
        }
    }
}
