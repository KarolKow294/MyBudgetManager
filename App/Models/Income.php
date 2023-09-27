<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Auth;
use DateTime;

class Income extends \Core\Model
{
    public $id;
    public $user_id;
    public $income_category_assigned_to_user_id;
    public $amount;
    public $date_of_income;
    public $income_comment;
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
            $convertedDate = strtotime($this->date_of_income);
            
            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
                    VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->bindValue(':income_category_assigned_to_user_id', $this->income_category_assigned_to_user_id, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_income', date('Y-m-d', $convertedDate), PDO::PARAM_STR);
            $stmt->bindValue(':income_comment', $this->income_comment, PDO::PARAM_STR);

            $this->success = true;

            return $stmt->execute();
        }
        return false;
    }

    public static function fetchIncomesByDate($user_id, $start_date, $end_date)
    {
        $sql = 'SELECT incomes.id, incomes.amount, incomes.date_of_income, incomes.income_comment, incomes_category_assigned_to_users.name
                FROM incomes
                INNER JOIN incomes_category_assigned_to_users ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
                WHERE incomes.user_id = :user_id AND incomes.date_of_income BETWEEN :start_date AND :end_date
                ORDER BY incomes.date_of_income';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam('start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function fetchTotalIncomesByCategoryAndDate($user_id, $start_date, $end_date)
    {
        $sql = 'SELECT incomes_category_assigned_to_users.name, SUM(incomes.amount) total_amount_by_category
                FROM incomes_category_assigned_to_users
                INNER JOIN incomes ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
                WHERE incomes.user_id = :user_id AND incomes.date_of_income BETWEEN :start_date AND :end_date
                GROUP BY incomes_category_assigned_to_users.name
                ORDER BY total_amount_by_category DESC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam('start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function fetchIncomeById($id)
    {
        $sql = 'SELECT *
                FROM incomes
                WHERE incomes.id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public function update($data, $id)
    {
        $this->income_category_assigned_to_user_id = $data['income_category_assigned_to_user_id'];
        $this->amount = $data['amount'];

        $this->date_of_income = $data['date_of_income'];      

        if ($data['income_comment'] == '') {
            $this->income_comment = NULL;
        } else {
            $this->income_comment = $data['income_comment'];
        } 
        $this->id = $id;

        $this->validate();

        if (empty($this->errors)) {
            $convertedDate = strtotime($this->date_of_income);

            $sql = 'UPDATE incomes
                    SET income_category_assigned_to_user_id = :income_category_assigned_to_user_id, amount = :amount, date_of_income = :date_of_income, income_comment = :income_comment
                    WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':income_category_assigned_to_user_id', $this->income_category_assigned_to_user_id, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_income', date('Y-m-d', $convertedDate), PDO::PARAM_STR);
            $stmt->bindValue(':income_comment', $this->income_comment, PDO::PARAM_INT);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }
        return false;
    }

    public static function delete($id)
    {
        $sql = 'DELETE FROM incomes
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
        
        if ($this->date_of_income != '') {
            $date = date_create_from_format('Y-m-d', $this->date_of_income);
            
            if ($date === false) {
                $this->errors[] = 'Data musi być w formacie YYYY-MM-DD';
            }
        } else {
            $this->errors[] = 'Data jest wymagana';
        }
        
        if ($this->income_category_assigned_to_user_id != '') {
            $categories = Auth::getIncomeCategories();

            $correct_category = false;

            foreach ($categories as $key => $category) {
                if ($key == $this->income_category_assigned_to_user_id) {
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
    }
}