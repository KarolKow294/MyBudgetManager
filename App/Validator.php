<?php

namespace App;

class Validator
{
    public static function validateDate($start_date, $end_date)
    {
        $errors = [];

        if ($start_date != '') {
            $date = date_create_from_format('Y-m-d', $start_date);
            
            if ($date === false) {
                $errors[] = 'Data startowa musi być w formacie YYYY-MM-DD';
            }
        } else {
            $errors[] = 'Data startowa jest wymagana';
        }

        if ($end_date != '') {
            $date = date_create_from_format('Y-m-d', $end_date);
            
            if ($date === false) {
                $errors[] = 'Data końcowa musi być w formacie YYYY-MM-DD';
            }
        } else {
            $errors[] = 'Data końcowa jest wymagana';
        }

        if (strtotime($start_date) > strtotime($end_date)) {
            $errors[] = 'Data końcowa jest wcześniejsza niż startowa';
        }
        return $errors;
    }
}