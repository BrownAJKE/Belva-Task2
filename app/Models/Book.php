<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'isbn', 
        'user_id',
        'author_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function author(){
        return $this->belongsTo(Author::class);
    }
}
