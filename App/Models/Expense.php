<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Auth;
use DateTime;

class Expense extends \Core\Model {
    public $expense_category_assigned_to_user_id;
    public $payment_method_assigned_to_user_id;
    public $amount;
    public $date_of_expense;
    public $expense_comment;
    public $errors = [];
    public $success;

    
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
          $this->$key = $value;
        }
    }

    public function save() {
        $this->validate();
        
        $user_id = $_SESSION['user_id'];

        if (empty($this->errors)) {
            $convertedDate = strtotime($this->date_of_expense);
            
            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, amount, date_of_expense, expense_comment)
                    VALUES (:user_id, :expense_category_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->bindValue(':expense_category_assigned_to_user_id', $this->expense_category_assigned_to_user_id, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_expense', date('Y-m-d', $convertedDate), PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $this->expense_comment, PDO::PARAM_STR);

            $this->success = true;

            return $stmt->execute();
        }
        return false;
    }
    
    public function validate() {
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
                $this->errors[] = 'Categoria jest inna niż przypisana do użytkownika';
            }
        } else {
            $this->errors[] = 'Categoria jest wymagana';
        }
    }
}