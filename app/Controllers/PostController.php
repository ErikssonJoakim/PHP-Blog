<?php declare(strict_types = 1);

namespace App\Controllers;

use Cocur\Slugify\Slugify;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Blog\Foundation\AbstractController;
use Blog\Foundation\View;
use Blog\Foundation\Authentication;
use Blog\Foundation\Exceptions\HttpException;
use Blog\Foundation\Validator;
use Blog\Foundation\Session;
use Blog\Foundation\Internationalization as Language;

class PostController extends AbstractController {


    public function index(): void {

        $posts = Post::withCount('getComments')->orderBy('post_id', 'desc')->get();

        View::render(view: 'index', data: [
            'posts' => $posts
        ]);
    }

    public function showPost(string $slug): void {
        
        try {
            $post = Post::withCount('getComments')->where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }


        View::render(view: 'posts.show', data: [
            'post' => $post
        ]);
    }

    public function commentPost(string $slug): void {
        !Authentication::isAuthenticated() && $this->redirect(to: 'login.form');

        $post = Post::where('slug', $slug)->firstOrFail();

        $validator = Validator::get(data: $_POST);
        $validator->mapFieldsRules(rules: [
            'comment' => ['required', ['lengthMin', 3]]
        ]);

        if(!$validator->validate()) {
            Session::addFlash(key: Session::ERRORS, value: $validator->errors());
            Session::addFlash(key: Session::OLD, value: $_POST);
            $this->redirect(to: 'posts.show', data: ['slug' => $slug]);
        }

        Comment::create([
            'body' => $_POST['comment'],
            'user_id' => Authentication::getSessionId(),
            'post_id' => $post->post_id
        ]);

        Session::addFlash(key: Session::STATUS, value: Language::getTranslation(subject: 'validation' , key: 'comment_published'));
        $this->redirect(to: 'posts.show', data: ['slug' => $slug]);
    }

    public function deletePost(string $slug): void {
        !Authentication::isAdmin() && $this->redirect(to: 'login.form');

        $post = Post::where('slug', $slug)->firstOrFail();
        unlink(filename: sprintf('%s/public/assets/image/%s', ROOT, $post->img));
        $post->delete();

        $this->redirect(to: 'index');
    }
    
    public function createPost(): void {
        !Authentication::isAdmin() && $this->redirect(to: 'login.form');
        
        View::render(view: 'posts.create');
    }

    protected function slugify(string $title): string {
        $slugify = new Slugify();
        $slug = $slugify->slugify($title);
        $i = 1;
        $unique_slug = $slug;
        while (Post::where('slug', $unique_slug)->exists()) {
            $unique_slug = sprintf('%s-%s', $slug, $i++);
        }
        return $unique_slug;
    }

    public function storePost(): void {
        $validator = Validator::get(data: $_POST + $_FILES);
        $validator->mapFieldsRules(rules: [
            'title' => ['required', ['lengthMin', 3]],
            'post' => ['required', ['lengthMin', 3]],
            'file' => ['required_file', 'image', 'squared_image']
        ]);

        if(!$validator->validate()) {
            Session::addFlash(key: Session::ERRORS, value: $validator->errors());
            Session::addFlash(key: Session::OLD, value: $_POST);
            $this->redirect(to: 'posts.create');
        }

        $slug = $this->slugify(title: $_POST['title']);
        $ext = pathinfo(path: $_FILES['file']['name'], flags: PATHINFO_EXTENSION);
        $filename = sprintf('%s.%s', $slug, $ext);

        if(!move_uploaded_file(
            from: $_FILES['file']['tmp_name'], 
            to: sprintf('%s/public/assets/image/%s', ROOT, $filename))) {
                Session::addFlash(key: Session::ERRORS, value: ['file' => [
                    Language::getTranslation(subject: 'validation', key: 'file_upload_error')
                ]]);
                Session::addFlash(key: Session::OLD, value: $_POST);

                $this->redirect(to: 'posts.create');
        }

        $post = Post::create([
            'user_id' => Authentication::getSessionId(),
            'title' => $_POST['title'],
            'slug' => $slug,
            'body' => $_POST['post'],
            'reading_time' => ceil(num: str_word_count(string: $_POST['post']) / 238),
            'img' => $filename
        ]);

        Session::addFlash(key: Session::STATUS, value: Language::getTranslation(subject: 'validation' , key: 'post_published'));

        $this->redirect(to: 'posts.show', data: ['slug' => $post->slug]);
    }

    public function editPost(string $slug): void {
        !Authentication::isAdmin() && $this->redirect(to: 'login.form');
        
        try {
            $post = Post::where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render(view: 'posts.edit', data: [
            'post' => $post,
        ]);
    }

    public function updatePost(string $slug): void {
        !Authentication::isAdmin() && $this->redirect(to: 'login.form');

        $post = Post::where('slug', $slug)->firstOrFail();
        $validator = Validator::get(data: $_POST);
        $validator->mapFieldsRules(rules: [
            'title' => ['required', ['lengthMin', 3]],
            'post' => ['required', ['lengthMin', 3]],
        ]);

        if(!$validator->validate()) {
            Session::addFlash(key: Session::ERRORS, value: $validator->errors());
            Session::addFlash(key: Session::OLD, value: $_POST);
            $this->redirect(to: 'posts.edit', data: ['slug' => $post->slug]);
        }

        $post->fill([
            'title' => $_POST['title'],
            'body' => $_POST['post'],
            'reading_time' => ceil(num: str_word_count(string: $_POST['post']) / 238)
        ]);
        $post->save();

        Session::addFlash(key: Session::STATUS, value: Language::getTranslation(subject: 'validation' , key: 'post_updated'));

        $this->redirect(to: 'posts.show', data: ['slug' => $post->slug]);

    }
}