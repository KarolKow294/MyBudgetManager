<?php

namespace App\Models;

use PDO;

class Balance extends \Core\Model
{
    public static function sumOfAmounts($totals)
    {
        $sum = 0;

        foreach ($totals as $total) {
            $sum += $total['total_amount_by_category'];
        }
        return $sum;
    }

    public static function balance($sum_of_incomes, $sum_of_expenses) {
        return $sum_of_incomes - $sum_of_expenses;
    }
}