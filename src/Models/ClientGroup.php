<?php

namespace Eugenefvdm\Api\Models;

use Illuminate\Database\Eloquent\Model;

class ClientGroup extends Model
{
    protected $table = 'tblclientgroups';

    // Disable timestamps
    public $timestamps = false;

    protected $fillable = [
        'groupname',
        'groupcolour',
        'separateinvoices',
    ];

    protected $attributes = [
        'separateinvoices' => '',
    ];
}
