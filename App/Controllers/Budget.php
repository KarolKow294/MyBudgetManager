<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Balance;
use \App\Validator;
use \App\Flash;
use \App\Models\ExpenseCategory;

class Budget extends Authenticated
{
    protected $user;

    protected function before()
    {
        parent::before();
        $this->user = Auth::getUser();
    }
    
    public function indexAction()
    {
        View::renderTemplate('Budget/index.html');
    }

    public function incomeAction()
    {
        View::renderTemplate('Budget/income.html');
    }

    public function expenseAction()
    {
        View::renderTemplate('Budget/expense.html');
    }

    public function currentMonthAction()
    {
        $title = 'current month';

        $start_date = date('Y-m') . '-01';
        $end_date = date('Y-m-t');
        
        $this->sendDataToBalanceView($start_date, $end_date, $title);
    }

    public function previousMonthAction()
    {
        $title = 'previous month';

        $start_date = date('Y-m-d', strtotime('first day of last month'));
        $end_date = date('Y-m-d', strtotime('last day of last month'));
        
        $this->sendDataToBalanceView($start_date, $end_date, $title);
    }

    public function currentYearAction()
    {
        $title = 'current year';

        $start_date = date('Y') . '-01-01';
        $end_date = date('Y') . '-12-31';
        
        $this->sendDataToBalanceView($start_date, $end_date, $title);
    }

    public function selectedPeriodAction()
    {
        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'];

        $title = $start_date . ' ÷ ' . $end_date;
        
        $this->sendDataToBalanceView($start_date, $end_date, $title);
    }

    private function sendDataToBalanceView($start_date, $end_date, $title)
    {
        $errors = [];
        $errors = Validator::validateDate($start_date, $end_date);

        if (empty($errors)) {
            
            $incomes = Income::fetchIncomesByDate($this->user->id, $start_date, $end_date);
            $total_incomes = Income::fetchTotalIncomesByCategoryAndDate($this->user->id, $start_date, $end_date);

            $expenses = Expense::fetchExpensesByDate($this->user->id, $start_date, $end_date);
            $total_expenses = Expense::fetchTotalExpensesByCategoryAndDate($this->user->id, $start_date, $end_date);

            $sum_of_incomes = Balance::sumOfAmounts($total_incomes);
            $sum_of_expenses = Balance::sumOfAmounts($total_expenses);

            $balance = Balance::balance($sum_of_incomes, $sum_of_expenses);

            View::renderTemplate('Budget/balance.html', ['incomes' => $incomes, 'total_incomes' => $total_incomes, 'sum_of_incomes' => $sum_of_incomes, 'expenses' => $expenses, 'total_expenses' => $total_expenses, 'sum_of_expenses' => $sum_of_expenses, 'balance' => $balance, 'title' => $title, 'date_errors' => $errors]);

        } else {
            View::renderTemplate('Budget/balance.html', ['title' => $title, 'date_errors' => $errors]);
        }
    }

    public function createIncomeAction()
    {
        $income = new Income($_POST);
    
        $income->save();

        View::renderTemplate('Budget/income.html', ['income' => $income]);
    }

    public function createExpenseAction()
    {
        $expense = new Expense($_POST);
    
        $expense->save();

        View::renderTemplate('Budget/expense.html', ['expense' => $expense]);
    }

    public function editIncomeAction()
    {
        $income_id = $_GET['id'];

        $income = Income::fetchIncomeById($income_id);

        View::renderTemplate('Budget/edit_income.html', ['income' => $income]);
    }

    public function updateIncomeAction()
    {   
        $income = new Income();
        $income_id = $_GET['id'];

        if ($income->update($_POST, $income_id)) {
            Flash::addMessage('Zmiany zostały zapisane');

            $this->redirect('/Budget/currentMonth');
        } else {
            View::renderTemplate('Budget/edit_income.html', ['income' => $income]);
        }
    }

    public function deleteIncomeAction()
    {   
        $income_id = $_GET['id'];

        Income::delete($income_id);

        Flash::addMessage('Przychód został usunięty');

        $this->redirect('/Budget/currentMonth');
    }

    public function editExpenseAction()
    {
        $expense_id = $_GET['id'];

        $expense = Expense::fetchExpenseById($expense_id);

        View::renderTemplate('Budget/edit_expense.html', ['expense' => $expense]);
    }

    public function updateExpenseAction()
    {   
        $expense = new Expense();
        $expense_id = $_GET['id'];

        if ($expense->update($_POST, $expense_id)) {
            Flash::addMessage('Zmiany zostały zapisane');

            $this->redirect('/Budget/currentMonth');
        } else {
            View::renderTemplate('Budget/edit_expense.html', ['expense' => $expense]);
        }
    }

    public function deleteExpenseAction()
    {   
        $expense_id = $_GET['id'];

        Expense::delete($expense_id);

        Flash::addMessage('Wydatek został usunięty');

        $this->redirect('/Budget/currentMonth');
    }

    public function limitAction()
    {
        $user_id = $this->user->id;
        $category = $this->route_params['category'];

        echo json_encode(ExpenseCategory::getLimit($user_id, $category), JSON_UNESCAPED_UNICODE);
    }
}
