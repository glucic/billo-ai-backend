<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $table = 'organisations';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'description',
        'employee_count',
    ];

    /**
     * The users that belong to the organisation.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }
}
