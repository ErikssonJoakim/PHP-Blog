<?php declare(strict_types = 1);

namespace Blog\Foundation;

use App\Models\User;

class Authentication {
    protected const SESSION_ID = 'user_id';

    public static function isAuthenticated(): bool {
        return (bool) Session::getValue(key: static::SESSION_ID);
    }

    public static function isAdmin(): bool {
        return static::isAuthenticated() && static::getUser()->role === 'admin';
    }

    public static function userExists(string $email, string $password): bool {
        $user = User::where('email', $email)->first();

        return (bool) $user && password_verify(password: $password, hash: $user->password);
    }

    public static function authenticate(int $id): void {
        Session::addValue(key: static::SESSION_ID, value: $id);
    }

    public static function logout(): void {
        Session::remove(key: static::SESSION_ID);
    }

    public static function getSessionId(): ?int {
        return Session::getValue(key: static::SESSION_ID);
    }

    public static function getUser(): ?User {
        return User::find(static::getSessionId());
    }
}