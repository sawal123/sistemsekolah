<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = ['id'];

    public function user() { return $this->belongsTo(User::class); }
    public function kategori() { return $this->belongsTo(Kategori::class); }
    public function tags() { return $this->belongsToMany(Tag::class); }
    public function comments() { return $this->hasMany(PostComment::class); }
    public function approvedComments() { return $this->hasMany(PostComment::class)->where('status', 'Approved'); }
}
