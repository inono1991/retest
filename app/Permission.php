<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'descibe', 'status'];
    public $timestamps;

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
