<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class DitbloxConfig extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'ditbloxConfig';
    protected $primaryKey = 'Id';
    protected $fillable = [
        'Uid',
        'Config',
    ];


    public $timestamps = true;

    public const UPDATED_AT = 'updatedAt';
    public const CREATED_AT = 'createdAt';
}
