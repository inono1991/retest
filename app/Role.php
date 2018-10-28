<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'level', 'describe', 'status'];

    public $timestamps;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
