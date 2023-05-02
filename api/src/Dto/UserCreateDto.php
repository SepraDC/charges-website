<?php

namespace App\Dto;

use App\Shared\Dto\FormInput;

final class UserCreateDto implements FormInput
{
    public string $username;
    public string $plainPassword;
    public array $roles;
}