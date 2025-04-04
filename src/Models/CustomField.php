<?php

namespace Eugenefvdm\Api\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $table = 'tblcustomfields';
    
    protected $fillable = [
        'type',
        'fieldname',
        'fieldtype',
        'description',
        'fieldoptions',
        'regexpr',
        'adminonly',
        'required',
        'showorder',
        'showinvoice',
    ];

    protected $attributes = [
        'description' => '',
        'fieldoptions' => '',
        'regexpr' => '',
        'adminonly' => '',
        'required' => '',
        'showorder' => '',
        'showinvoice' => '',
    ];
} 