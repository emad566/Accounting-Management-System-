<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    use HasFactory;

    protected $fillable = [
        'id',
        'voucher_id',
        'invoice_status_id',
        'client_id',
        'user_rep_id',
        'invoice_code',
        'user_accept_id',
        'image',
        'invoice_details',
        'invoice_date',
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'invoice_product')->withPivot(
            'id',
            'invoice_id',
            'product_id',
            'runID',
            'invoice_quantity',
            'invoice_bounce',
            'invoice_public_price',
            'discount'
        );
    }

    public function voucher()
    {
        return $this->hasOne('App\Models\Voucher', 'id', 'voucher_id');
    }

    public function client()
    {
        return $this->hasOne('App\Models\Client', 'id', 'client_id');
    }

    public function rep()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_rep_id');
    }

    public function view_invoice()
    {
        return $this->hasOne('App\Models\ViewInvoice', 'id', 'id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\InvoiceStatus', 'id', 'invoice_status_id');
    }

    public function gets()
    {
        return $this->hasMany('App\Models\Get', 'invoice_id', 'id');
    }

    public function view_invoice_products()
    {
        return $this->hasMany('App\Models\ViewInvoiceProduct', 'invoice_id', 'id');
    }

    public function returns()
    {
        return $this->hasMany('App\Models\Returns', 'invoice_id', 'id');
    }


    public static function files_path($dir) {
        return 'assets/dashboard/images/invoices/'.$dir.'/';
    }

    public function image_delete()
    {
        delete_img($this->image_rel_path($this->client_id));
    }

    public function image_rel_path()
    {
        return $this->files_path($this->client_id) . $this->attributes['image'];
    }

    public function  getImageAttribute($val){
        return ($val && file_exists($this->image_rel_path($this->client_id))) ?  asset($this->image_rel_path($this->client_id)) : false;
    }

    public function  imageSrc(){
        return $this->image_rel_path($this->client_id);
    }

    static public function createInvoice($inv, $pros, $pay)
    {
        // Start: Get Last Invoice_code
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $lastInvoice_id = ($lastInvoice)? $lastInvoice->id : 1;
                    if(Invoice::where('invoice_code', $lastInvoice_id)->count()>0){
            do{
                $lastInvoice_id +=1;
            }while(Invoice::where('invoice_code', $lastInvoice_id)->count()!=0);
        }
        $invoice_code = $lastInvoice_id;
        // End: Get Last Invoice_code
        
        // Start: Cteate Invoice
        $inv['invoice_code'] = $invoice_code;
        $invoice = Invoice::create($inv);
        $invoice->products()->sync($pros);

        $lastGet = Get::orderBy('id', 'desc')->first();
        $lastGet_id = ($lastGet)? $lastGet->id : 1;
                    if(Get::where('Get_code', $lastGet_id)->count()>0){
            do{
                $lastGet_id +=1;
            }while(Get::where('Get_code', $lastGet_id)->count()!=0);
        }
        $get_code = $lastGet_id;

        $get = Get::create([
            'invoice_id' => $invoice->id,
            'get_code' => $get_code,
            'get_date' => $invoice->invoice_date,
            'user_rep_id' => $invoice->user_rep_id,
            'get_overPrice' => 0,
            'paid_from_client_balance' => 0,
            'client_pay' => $pay,
        ]);
    }
}
