<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;

class Settings extends Authenticated
{
    protected $user;

    protected function before()
    {
        parent::before();
        $this->user = Auth::getUser();
    }
    
    public function indexAction()
    {
        View::renderTemplate('Settings/index.html');
    }

    public function addIncomeCategoryAction()
    {
        $income_category = new IncomeCategory($_POST);

        $income_category->save();

        View::renderTemplate('Settings/index.html', ['income_category' => $income_category]);
    }

    public function addExpenseCategoryAction()
    {
        $expense_category = new ExpenseCategory($_POST);

        $expense_category->save();

        View::renderTemplate('Settings/index.html', ['expense_category' => $expense_category]);
    }

    public function addPaymentMethodAction()
    {
        $payment_method = new PaymentMethod($_POST);

        $payment_method->save();

        View::renderTemplate('Settings/index.html', ['payment_method' => $payment_method]);
    }

    public function editIncomeCategoryAction()
    {
        $income_category = new IncomeCategory($_POST);

        $income_category->update();

        View::renderTemplate('Settings/index.html', ['income_category' => $income_category]);
    }

    public function editExpenseCategoryAction()
    {
        $expense_category = new ExpenseCategory($_POST);

        $expense_category->update();

        View::renderTemplate('Settings/index.html', ['expense_category' => $expense_category]);
    }

    public function editPaymentMethodAction()
    {
        $payment_method = new PaymentMethod($_POST);

        $payment_method->update();

        View::renderTemplate('Settings/index.html', ['payment_method' => $payment_method]);
    }

    public function deleteIncomeCategoryAction()
    {
        $income_category = new IncomeCategory($_POST);

        $income_category->delete();

        View::renderTemplate('Settings/index.html', ['income_category' => $income_category]);
    }

    public function deleteExpenseCategoryAction()
    {
        $expense_category = new ExpenseCategory($_POST);

        $expense_category->delete();

        View::renderTemplate('Settings/index.html', ['expense_category' => $expense_category]);
    }

    public function deletePaymentMethodAction()
    {
        $payment_method = new PaymentMethod($_POST);

        $payment_method->delete();

        View::renderTemplate('Settings/index.html', ['payment_method' => $payment_method]);
    }
}
