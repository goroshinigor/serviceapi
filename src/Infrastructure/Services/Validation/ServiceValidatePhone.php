<?php

namespace App\Infrastructure\Services\Validation;

use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ServiceValidatePhone
{
    public static function validate($phone): bool
    {
        $errors = [];
        $validator = Validation::createValidator();
        $simpleConstraints = [
            new NotBlank(),
            new Length(['min' => 13, 'max' => 13]),
        ];
        $errors[] = $validator->validate(
            $phone,
            $simpleConstraints
        );

        $countryCode = substr($phone, 0, 4);
        $phoneConstraints = [
            new EqualTo(['value' => '+380'])
        ];

        $errors[] = $validator->validate(
            $countryCode,
            $phoneConstraints
        );

        if (!is_numeric(substr($phone, 4, 13))) {
            $errors[] = 1;
        }

        if (0 === count($errors[0])) return true;
        return false;
    }
}