<?php

namespace App\Validators;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Exception\AppException;

class UserValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $data): void
    {
        // Định nghĩa các ràng buộc
        $constraints = new Assert\Collection([
            'username' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 3, 'max' => 50]),
            ],
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
            'password' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 6]),
            ],
        ]);

        // Thực hiện kiểm tra dữ liệu
        $errors = $this->validator->validate($data, $constraints);

        // Nếu có lỗi, ném ngoại lệ
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new AppException('E1234');
        }
    }
}
