<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'date',
        'file_upload'
    ];

    /**
     * Get the invoice subs for the invoice.
     */
    public function invoiceSubs()
    {
        return $this->hasMany(InvoiceSub::class);
    }
}

