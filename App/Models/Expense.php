<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Auth;
use DateTime;

class Expense extends \Core\Model
{
    public $id;
    public $user_id;
    public $expense_category_assigned_to_user_id;
    public $payment_method_assigned_to_user_id;
    public $amount;
    public $date_of_expense;
    public $expense_comment;
    public $errors = [];
    public $success;

    
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function save()
    {
        $this->validate();
        
        $user_id = $_SESSION['user_id'];

        if (empty($this->errors)) {
            $convertedDate = strtotime($this->date_of_expense);
            
            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
                    VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->bindValue(':expense_category_assigned_to_user_id', $this->expense_category_assigned_to_user_id, PDO::PARAM_STR);
            $stmt->bindValue(':payment_method_assigned_to_user_id', $this->payment_method_assigned_to_user_id, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_expense', date('Y-m-d', $convertedDate), PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $this->expense_comment, PDO::PARAM_STR);

            $this->success = true;

            return $stmt->execute();
        }
        return false;
    }

    public static function fetchExpensesByDate($user_id, $start_date, $end_date)
    {
        $sql = 'SELECT expenses.id, expenses.amount, expenses.date_of_expense, expenses.expense_comment, expenses_category_assigned_to_users.name, payment_methods_assigned_to_users.name AS payment_method
                FROM expenses
                INNER JOIN expenses_category_assigned_to_users ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
                INNER JOIN payment_methods_assigned_to_users ON payment_methods_assigned_to_users.id = expenses.payment_method_assigned_to_user_id
                WHERE expenses.user_id = :user_id AND expenses.date_of_expense BETWEEN :start_date AND :end_date
                ORDER BY expenses.date_of_expense';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam('start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function fetchTotalExpensesByCategoryAndDate($user_id, $start_date, $end_date)
    {
        $sql = 'SELECT expenses_category_assigned_to_users.name, SUM(expenses.amount) total_amount_by_category
                FROM expenses_category_assigned_to_users
                INNER JOIN expenses ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
                WHERE expenses.user_id = :user_id AND expenses.date_of_expense BETWEEN :start_date AND :end_date
                GROUP BY expenses_category_assigned_to_users.name
                ORDER BY total_amount_by_category DESC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam('start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function fetchExpenseById($id)
    {
        $sql = 'SELECT *
                FROM expenses
                WHERE expenses.id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function fetchExpenseIdAndCategoryIdAssignedToUser()
    {
        $user_id = $_SESSION['user_id'];

        $sql = 'SELECT id, expense_category_assigned_to_user_id
                FROM expenses
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        
        $stmt->execute();

        $rows = $stmt->fetchAll();

        $categories = [];

        foreach ($rows as $row) {
            $categories[$row['id']] = $row['expense_category_assigned_to_user_id'];
        }

        return $categories;
    }

    public static function fetchExpenseIdAndMethodIdAssignedToUser()
    {
        $user_id = $_SESSION['user_id'];

        $sql = 'SELECT id, payment_method_assigned_to_user_id
                FROM expenses
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        
        $stmt->execute();

        $rows = $stmt->fetchAll();

        $categories = [];

        foreach ($rows as $row) {
            $categories[$row['id']] = $row['payment_method_assigned_to_user_id'];
        }

        return $categories;
    }

    public function update($data, $id)
    {
        $this->payment_method_assigned_to_user_id = $data['payment_method_assigned_to_user_id'];
        $this->expense_category_assigned_to_user_id = $data['expense_category_assigned_to_user_id'];
        $this->amount = $data['amount'];
        $this->date_of_expense = $data['date_of_expense'];
        $this->expense_comment = $data['expense_comment'];

        $this->id = $id;

        $this->validate();

        if (empty($this->errors)) {
            $convertedDate = strtotime($this->date_of_expense);

            $sql = 'UPDATE expenses
                    SET payment_method_assigned_to_user_id = :payment_method_assigned_to_user_id, expense_category_assigned_to_user_id = :expense_category_assigned_to_user_id, amount = :amount, date_of_expense = :date_of_expense, expense_comment = :expense_comment
                    WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':payment_method_assigned_to_user_id', $this->payment_method_assigned_to_user_id, PDO::PARAM_STR);
            $stmt->bindValue(':expense_category_assigned_to_user_id', $this->expense_category_assigned_to_user_id, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_expense', date('Y-m-d', $convertedDate), PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $this->expense_comment, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }
        return false;
    }

    public static function delete($id)
    {
        $sql = 'DELETE FROM expenses
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        $stmt->execute();
    }

    private function validate()
    {
        if ($this->amount != '') {
            if (! preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $this->amount)) {
                $this->errors[] = 'Wprowadź prawidłową kwotę';
            }
        } else {
            $this->errors[] = 'Kwota jest wymagana';
        }
        
        if ($this->date_of_expense != '') {
            $date = date_create_from_format('Y-m-d', $this->date_of_expense);
            
            if ($date === false) {
                $this->errors[] = 'Data musi być w formacie YYYY-MM-DD';
            }
        } else {
            $this->errors[] = 'Data jest wymagana';
        }
        
        if ($this->expense_category_assigned_to_user_id != '') {
            $categories = Auth::getExpenseCategories();

            $correct_category = false;

            foreach ($categories as $key => $category) {
                if ($key == $this->expense_category_assigned_to_user_id) {
                    $correct_category = true;
                    break;
                }
            }
            if ($correct_category == false) {
                $this->errors[] = 'Kategoria jest inna niż przypisana do użytkownika';
            }
        } else {
            $this->errors[] = 'Kategoria jest wymagana';
        }

        if ($this->payment_method_assigned_to_user_id != '') {
            $methods = Auth::getPaymentMethods();

            $correct_method = false;

            foreach ($methods as $key => $method) {
                if ($key == $this->payment_method_assigned_to_user_id) {
                    $correct_method = true;
                    break;
                }
            }
            if ($correct_method == false) {
                $this->errors[] = 'Metoda płatności jest inna niż przypisana do użytkownika';
            }
        } else {
            $this->errors[] = 'Metoda płatności jest wymagana';
        }
    }
}