<?php

namespace App;

class Validator implements ValidatorInterface
{
    public function validate(array $course)
    {
        // BEGIN (write your solution here)
        $errors = [];
        if (empty($course['paid'])) {
            $errors['paid'] = "Can't be blank";
        }
        if (empty($course['title'])) {
            $errors['title'] = "Can't be blank";
        }

        return $errors;
        // END
    }
}

