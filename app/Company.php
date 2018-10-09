<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The users that belong to the company.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
