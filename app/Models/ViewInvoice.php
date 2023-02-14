<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewInvoice extends Invoice
{
    protected$table="view_invoices";
    protected $fillable = [
        'id',
        'voucher_id',
        'client_id',
        'invoice_status_id',
        'client_pays',
        'client_balance_effect',
        'get_requireds',
        'get_paids',
        'get_nexts',
        'updated_at',
        'created_at'
    ];

    public function voucher()
    {
        return $this->hasOne('App\Models\Voucher', 'id', 'voucher_id');
    }

    public function client()
    {
        return $this->hasOne('App\Models\Client', 'id', 'client_id');
    }


    public function invoice()
    {
        return $this->hasOne('App\Models\Invoice', 'id', 'id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\InvoiceStatus', 'id', 'invoice_status_id');
    }

}


