<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_holder',
        'account_number',
        'name_bank',
        'branch',
        'short_name',
        'code',
    ];

    public function getRules()
    {
        return [
            'account_holder' => ['sometimes','required', 'string'],
            'account_number' => ['sometimes','required', 'string'],
            'name_bank' => ['sometimes','required', 'string'],
            'short_name' => ['sometimes','required', 'string'],
            'code' => ['sometimes','required', 'string'],
            'branch' => ['sometimes','required', 'string'],
        ];
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeSearchName($query, $search)
    {
        return $query->whereLike(['account_holder', 'name_bank', 'short_name', 'code'], $search);
    }
}
