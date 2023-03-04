<?php declare(strict_types = 1);

namespace App\Controllers;

use Blog\Foundation\AbstractController;
use Blog\Foundation\Authentication;
use Blog\Foundation\View;
use Blog\Foundation\Session;
use Blog\Foundation\Validator;
use Blog\Foundation\Internationalization as Language;

class HomeController extends AbstractController {
    public function index(): void {
        !Authentication::isAuthenticated() && $this->redirect(to: 'login.form');
        $user = Authentication::getUser();

        View::render(view: 'home', data: [
            'user' => $user
        ]);
    }

    public function updateName():void {
        !Authentication::isAuthenticated() && $this->redirect(to: 'login.form');
        
        $validator = Validator::get(data: $_POST);
        $validator->mapFieldsRules(rules: [
            'name' => ['required', ['lengthMin', 5]]
        ]);

        if(!$validator->validate()) {
            Session::addFlash(key: Session::ERRORS, value: $validator->errors());
            Session::addFlash(key: Session::OLD, value: $_POST);
            $this->redirect(to: 'home');
        }
        
        $user = Authentication::getUser();
        $user->name = $_POST['name'];
        $user->save();
        
        Session::addFlash(key: Session::STATUS , value: Language::getTranslation(subject: 'validation' , key: 'name_updated'));
        $this->redirect(to: 'home');
    }

    public function updateEmail():void {
        !Authentication::isAuthenticated() && $this->redirect(to: 'login.form');
        
        $validator = Validator::get(data: $_POST);
        $validator->mapFieldsRules(rules: [
            'email' => ['required', 'email', ['unique', 'email', 'users']]
        ]);

        if(!$validator->validate()) {
            Session::addFlash(key: Session::ERRORS, value: $validator->errors());
            Session::addFlash(key: Session::OLD, value: $_POST);
            $this->redirect(to: 'home');
        }
        
        $user = Authentication::getUser();
        $user->email = $_POST['email'];
        $user->save();
        
        Session::addFlash(key: Session::STATUS , value: Language::getTranslation(subject: 'validation' , key: 'email_updated'));
        $this->redirect(to: 'home');
    }

    public function updatePassword():void {
        !Authentication::isAuthenticated() && $this->redirect(to: 'login.form');
        
        $validator = Validator::get(data: $_POST);
        $validator->mapFieldsRules(rules: [
            'password_old' => ['required', 'password'],
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']]
        ]);

        if(!$validator->validate()) {
            Session::addFlash(key: Session::ERRORS, value: $validator->errors());
            $this->redirect(to: 'home');
        }
        
        $user = Authentication::getUser();
        $user->password = password_hash(password: $_POST['password'], algo: PASSWORD_DEFAULT);
        $user->save();
        
        Session::addFlash(key: Session::STATUS , value: Language::getTranslation(subject: 'validation' , key: 'password_updated'));
        $this->redirect(to: 'home');
    }
}