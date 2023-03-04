<?php declare(strict_types = 1);

namespace App\Controllers;

use Blog\Foundation\AbstractController;
use Blog\Foundation\Authentication;
use Blog\Foundation\View;
use Blog\Foundation\Validator;
use Blog\Foundation\Session;
use Blog\Foundation\Internationalization as Language;

use App\Models\User;

class AuthController extends AbstractController {
    public function registerForm(): void {
        Authentication::isAuthenticated() && $this->redirect(to: 'home');
        View::render(view: 'auth.register');
    }
    
    public function register(): void {
        Authentication::isAuthenticated() && $this->redirect(to: 'home');

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
            'email' => ['required', 'email', ['unique', 'email', 'users']],
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']]
            
        ]);
 
        if (!$validator->validate()) {
            Session::addFlash(key: Session::ERRORS, value: array_column(array: $validator->errors(), column_key: 0));
            Session::addFlash(key: Session::OLD, value: $_POST);
            $this->redirect(to: 'register.form');
        }

        $user = User::create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => password_hash(password: $_POST['password'], algo: PASSWORD_DEFAULT)
        ]);

        Authentication::authenticate(id: $user->user_id);

        $this->redirect(to: 'home');
    }

    public function loginForm(): void {
        Authentication::isAuthenticated() && $this->redirect(to: 'home');
        View::render(view: 'auth.login');
    }

    public function login(): void {
        Authentication::isAuthenticated() && $this->redirect(to: 'home');
    
        $validator = Validator::get(data: $_POST);
        $validator->mapFieldsRules(rules: [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if($validator->validate() && Authentication::userExists(email: $_POST['email'], password: $_POST['password'])) {
            $user = User::where('email', $_POST['email'])->first();
            Authentication::authenticate(id: $user->user_id);
            $this->redirect(to: 'home');
        }

        Session::addFlash(key: Session::ERRORS, value: [Language::getTranslation(subject: 'validation' , key: 'login_failed')]);
        Session::addFlash(key: Session::OLD, value: $_POST);
        $this->redirect(to:'login.form');
    }

    public function logout(): void {
        Authentication::isAuthenticated() && Authentication::logout();
        $this->redirect(to: 'login.form');
    }
}