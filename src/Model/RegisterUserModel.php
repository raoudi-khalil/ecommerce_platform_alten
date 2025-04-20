<?php
// src/Model/RegisterUserModel.php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(title: "RegisterUserModel", description: "Model used to register a new user")]
class RegisterUserModel
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[OA\Property(example: "user@example.com")]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    #[OA\Property(example: "SecurePassword123")]
    public ?string $password = null;

    #[Assert\NotBlank]
    #[OA\Property(example: "username123")]
    public ?string $username = null;

    #[Assert\NotBlank]
    #[OA\Property(example: "John")]
    public ?string $firstname = null;
}
