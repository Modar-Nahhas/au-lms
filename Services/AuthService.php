<?php

namespace LMS_Website\Services;

use LMS_Website\Containers\Auth;
use LMS_Website\Containers\Route;
use LMS_Website\Containers\Session;
use LMS_Website\Enums\MessageTypeEnum;
use LMS_Website\Models\User;

readonly class AuthService
{
    public function login(string $email, string $password): void
    {
        $user = Auth::check();
        if ($user) {
            Route::redirect('index.php', 'You are already logged in.', MessageTypeEnum::Warning);
        }
        // Sanitize input
        $email = trim($email);
        $password = trim($password);

        // Validate input
        if ($email === '' || $password === '') {
            Route::redirectBack('Please enter both email and password.');
        }
        $user = User::checkUserCredentials($password, $email);

        if ($user) {
            $this->setUserInSession($user);
            Route::redirect('index.php');
        }

        Route::redirectBack('Wrong email or password.');
    }

    public function logout(): void
    {
        Session::destroySession();
        Route::redirect('index.php');
    }

    public function register(string $firstName, string $lastName, string $email, string $password, string $confirmPassword): void
    {
        Session::setValue('user_input', compact('firstName', 'lastName', 'email'));
        list($firstName, $lastName, $email, $password, $confirmPassword) = $this->cleanUserInput($firstName, $lastName, $email, $password, $confirmPassword);
        $errors = $this->validateUserInput($firstName, $lastName, $email, $password, $confirmPassword);

        if (!empty($errors)) {
            Route::redirectBack('Registration failed:<br>' . implode('<br>', $errors));
        }

        if ($this->userEmailExists($email)) {
            Route::redirectBack('Email already exists.');
        }

        $user = User::create($firstName, $lastName, $email, $password);

        $this->setUserInSession($user);
        Route::redirect('index.php');
    }

    /**
     * @param string $email
     * @return bool
     */
    public function userEmailExists(string $email): bool
    {
        return User::findByEmail($email) !== null;
    }

    /**
     * @param User $user
     * @return void
     */
    public function setUserInSession(User $user): void
    {
        Session::setValue('user', $user);
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @param string $confirmPassword
     * @return array
     */
    protected function cleanUserInput(string $firstName, string $lastName, string $email, string $password, string $confirmPassword): array
    {
        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $email = trim($email);
        $password = trim($password);
        $confirmPassword = trim($confirmPassword);
        return array($firstName, $lastName, $email, $password, $confirmPassword);
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @param string $confirmPassword
     * @return array
     */
    protected function validateUserInput(string $firstName, string $lastName, string $email, string $password, string $confirmPassword): array
    {
        // Server-side validation
        $errors = [];

        // letters (and space) 1–20 chars
        $alpha20 = '/^[A-Za-z ]{1,20}$/';

        // at least 8 chars, 1 upper, 1 lower, 1 digit, 1 special
        $strongPw = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';

        // === First name ===
        if ($firstName === '') {
            $errors['firstName'] = 'First name is required.';
        } elseif (!preg_match($alpha20, $firstName)) {
            $errors['firstName'] = 'First name must be between 1 and 20 characters.';
        }

        // === Last name ===
        if ($lastName === '') {
            $errors['lastName'] = 'Last name is required.';
        } elseif (!preg_match($alpha20, $lastName)) {
            $errors['lastName'] = 'Last name must be between 1 and 20 characters.';
        }

        // === Email ===
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email.';
        }

        // === Password ===
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (!preg_match($strongPw, $password)) {
            $errors['password'] =
                'Password must be at least 8 characters long and contain at least one uppercase letter, ' .
                'one lowercase letter, one number, and one special character.';
        }

        // === Confirm password ===
        if ($confirmPassword === '' || $confirmPassword !== $password) {
            $errors['confirmPassword'] = 'Passwords must match.';
        }
        return $errors;
    }
}