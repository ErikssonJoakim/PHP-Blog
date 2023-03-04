<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model {
    public $timestamps = false;
    protected $primaryKey = 'post_id'; 
    protected $fillable = [
        'user_id', 'title', 'slug', 'body', 'reading_time', 'img'
    ];

    public function getComments(): HasMany {
        return $this->hasMany(related: Comment::class, foreignKey: 'post_id')->orderBy('comment_id', 'desc');
    }
}