<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model {
    public $timestamps = false;
    protected $primaryKey = 'comment_id'; 
    protected $fillable = [
        'body', 'user_id', 'post_id'
    ];

    public function getUser(): BelongsTo {
        return $this->belongsTo(related: User::class, foreignKey: 'user_id');
    }
}