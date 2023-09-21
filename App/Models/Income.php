<?php

namespace App\Models;

use PDO;
use \Core\View;

class Income extends \Core\Model {
    private $income_category_assigned_to_user_id;
    private $amount;
    private $date_of_income;
    private $income_comment;

    
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
          $this->$key = $value;
        }
    }

    public function save() {
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

            return $stmt->execute();
        }
        return false;
    }
    
    public function validate() {
        if ($this->amount == '') {
            $this->errors[] = 'Kwota jest wymagana';
        }



    }
}