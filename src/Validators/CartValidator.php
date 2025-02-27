<?php

namespace App\Validators;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\AppException;

class CartValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateCartData(array $data, string $action): array
    {
        $constraints = $this->getConstraints($action);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = $this->formatErrors($violations);
            $errorMessage = json_encode($errors);
            throw new AppException('E10711', $errorMessage);
        }

        return array_filter($data, fn($value) => $value !== null && $value !== '');
    }

    private function getConstraints(string $action): Assert\Collection
    {
        if ($action === 'create') {
            return new Assert\Collection([
                'quantity' => [
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                ],
                'productOptionId' => [
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                ],
            ]);
        }

        if ($action === 'update') {
            return new Assert\Collection([
                'quantity' => new Assert\Optional([
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                ]),
            ]);
        }

        throw new \InvalidArgumentException('Invalid action for validation.');
    }

    private function formatErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $errors;
    }
}
