<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = ['message', 'receiver_id', 'sender_id'];

    public function rel_to_sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function rel_to_receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
