<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transfer;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Notif extends Model
{
    use LogsActivity;
    protected static $logAttributes = ["*"];
    protected static $logFillable = true;
    
    protected $table = 'notifs';
    protected $casts = [
        'data'=>'array',
    ];

    protected $fillable = [
        'id',
        'user_create_id',
        'notefun',
        'table_name',
        'noteType',
        'notifiable_type',
        'notifiable_id',
        'data',
        'readed_by_user_id',
        'readed_at',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'notif_user')->withPivot('id', 'user_id', 'readed_at');
    }

    public function manyUsers()
    {
        return $this->hasMany('App\Models\NotifPermission', 'notif_id', 'id');
    }

    public function readed_by_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'readed_by_user_id');
    }

    public function user_create()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_create_id');
    }

    public function user_receive()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_receive_id');
    }

    public function getObj()
    {
        return $this->hasOne('App\Models\\' . $this->noteType, 'id', 'notifiable_id');
    }

    // Notfis functions to Msgs to string
    public function createTransfer()
    {
        if($this->getObj){
            return "تم انشاء تحويل مخزني برقم سند: " . $this->getObj->transfer_code;
        } else return "تم حذف التحويل المخزني";
    }

    public function createInbank()
    {
        if($this->getObj){
            return "تم انشاء إيداع خارجي برقم سند: " . $this->getObj->inbank_code;
        } else return "تم حذف الإيداع الخارجي";
    }

    public function updateInbank()
    {
        if($this->getObj){
            return "تم تعديل إيداع خارجي برقم سند: " . $this->getObj->inbank_code;
        } else return "تم حذف الإيداع الخارجي";
    }

    public function createVoucher()
    {
        if($this->getObj){
            return "تم انشاء إذن صرف برقم سند: " . $this->getObj->voucher_code;
        } else return "تم حذف إذن الصرف";
    }

    public function createInvoice()
    {
        if($this->getObj){
            return "تم انشاء فاتورة برقم سند: " . $this->getObj->invoice_code;
        } else return "تم حذف الفاتورة";
    }

    public function createBanktaransfer()
    {
        if($this->getObj){
            return "تم انشاء تحويل حساب مالي برقم سند: " . $this->getObj->banktaransfer_code;
        } else return "تم حذف التحويل حساب مالي";
    }

    public function updateBanktaransfer()
    {
        if($this->getObj){
            return "تم تعديل تحويل حساب مالي برقم سند: " . $this->getObj->banktaransfer_code;
        } else return "تم حذف التحويل حساب مالي";
    }

    public function updateTransaction()
    {
        if($this->getObj){
            return "تم تعديل تحويل مالي برقم سند: " . $this->getObj->transaction_code;
        } else return "تم حذف التحويل مالي";
    }

    public function createBanktransfer()
    {
        if($this->getObj){
            return "تم انشاء تحويل حساب مالي برقم سند: " . $this->getObj->banktransfer_code;
        } else return "تم حذف التحويل حساب مالي";
    }

    public function updateBanktransfer()
    {
        if($this->getObj){
            return "تم تعديل تحويل حساب مالي برقم سند: " . $this->getObj->banktransfer_code;
        } else return "تم حذف التحويل حساب مالي";
    }

    public function createTransaction()
    {
        if($this->getObj){
            return "تم انشاء تحويل مالي برقم سند: " . $this->getObj->transaction_code;
        } else return "تم حذف التحويل مالي";
    }

    public function createSpend()
    {
        if($this->getObj){
            return "تم انشاء مصروف برقم سند: " . $this->getObj->spend_code;
        } else return "تم حذف المصروف";
    }

    public function updateSpend()
    {
        if($this->getObj){
            return "تم تعديل مصروف برقم سند: " . $this->getObj->spend_code;
        } else return "تم حذف المصروف";
    }

    public function updateInvoice()
    {
        if($this->getObj){
            return "تم تعديل فاتورة برقم سند: " . $this->getObj->invoice_code;
        } else return "تم حذف الفاتورة";
    }

    public function updateTransfer()
    {
        if($this->getObj){
            return "تم تعديل التحويل المخزني برقم سند: " . $this->getObj->transfer_code;
        } else return "تم حذف التحويل المخزني";
    }

    public function transfer_status_20()
    {
        if($this->getObj){
            return "قيد الشحن: التحويل المخزني برقم سند: " . $this->getObj->transfer_code;
        } else return "تم حذف التحويل المخزني";
    }

    public function transfer_status_40()
    {
        if($this->getObj){
            return "تم التسليم: التحويل المخزني برقم سند: " . $this->getObj->transfer_code;
        } else return "تم حذف التحويل المخزني";
    }

    public function transfer_status_30()
    {
        if($this->getObj){
            return "مرفوض: التحويل المخزني برقم سند: " . $this->getObj->transfer_code;
        } else return "تم حذف التحويل المخزني";
    }

    public function voucher_status_2()
    {
        if($this->getObj){
            return "موافقة: إذن صرف برقم سند: " . $this->getObj->voucher_code;
        } else return "تم حذف إذن صرف";
    }


    public function voucher_status_3()
    {
        if($this->getObj){
            return "خرج: إذن صرف برقم سند: " . $this->getObj->voucher_code;
        } else return "تم حذف إذن صرف";
    }

    public function voucher_status_accountant()
    {
        if($this->getObj){
            return "تسوية المحاسب: إذن صرف برقم سند: " . $this->getObj->voucher_code;
        } else return "تم حذف إذن صرف";
    }

    public function voucher_status_keeper()
    {
        if($this->getObj){
            return "تسوية أمين المخزن: إذن صرف برقم سند: " . $this->getObj->voucher_code;
        } else return "تم حذف إذن صرف";
    }

    public function voucher_status_6()
    {
        if($this->getObj){
            return "'طلب تسوية: إذن صرف برقم سند: " . $this->getObj->voucher_code;
        } else return "تم حذف إذن صرف";
    }

    public function voucher_status_100()
    {
        if($this->getObj){
            return "'مرفوض: إذن صرف برقم سند: " . $this->getObj->voucher_code;
        } else return "تم حذف إذن صرف";
    }

    public function invoice_status_10()
    {
        if($this->getObj){
            return "'طلب تعديل: فاتورة برقم سند: " . $this->getObj->invoice_code;
        } else return "تم حذف فاتورة";
    }

    public function invoice_status_15()
    {
        if($this->getObj){
            return "'مراجعة: فاتورة برقم سند: " . $this->getObj->invoice_code;
        } else return "تم حذف فاتورة";
    }

    public function invoice_status_20()
    {
        if($this->getObj){
            return "'موافقة: فاتورة برقم سند: " . $this->getObj->invoice_code;
        } else return "تم حذف فاتورة";
    }

    public function transaction_status_20()
    {
        if($this->getObj){
            return "'قيد المراجعة: التحويل المالي برقم سند: " . $this->getObj->transaction_code;
        } else return "تم حذف التحويل المالي";
    }

    public function transaction_status_30()
    {
        if($this->getObj){
            return "'موافقة: التحويل المالي برقم سند: " . $this->getObj->transaction_code;
        } else return "تم حذف التحويل المالي";
    }

    public function transaction_status_40()
    {
        if($this->getObj){
            return "'مرفوض: التحويل المالي برقم سند: " . $this->getObj->transaction_code;
        } else return "تم حذف التحويل المالي";
    }

    public function banktransfer_status_20()
    {
        if($this->getObj){
            return "'قيد المراجعة: التحويل المالي برقم سند: " . $this->getObj->banktransfer_code;
        } else return "تم حذف التحويل المالي";
    }

    public function banktransfer_status_30()
    {
        if($this->getObj){
            return "'موافقة: التحويل المالي برقم سند: " . $this->getObj->banktransfer_code;
        } else return "تم حذف التحويل المالي";
    }

    public function banktransfer_status_40()
    {
        if($this->getObj){
            return "'مرفوض: التحويل المالي برقم سند: " . $this->getObj->banktransfer_code;
        } else return "تم حذف التحويل المالي";
    }

    public function spend_status_20()
    {
        if($this->getObj){
            return "'قيد المراجعة: المصروف برقم سند: " . $this->getObj->spend_code;
        } else return "تم حذف المصروف";
    }

    public function spend_status_30()
    {
        if($this->getObj){
            return "'موافقة: المصروف برقم سند: " . $this->getObj->spend_code;
        } else return "تم حذف المصروف";
    }

    public function spend_status_40()
    {
        if($this->getObj){
            return "'موافقة: المصروف برقم سند: " . $this->getObj->spend_code;
        } else return "تم حذف المصروف";
    }

    public function inbank_status_20()
    {
        if($this->getObj){
            return "'قيد المراجعة: إيداع مالي خارجي برقم سند: " . $this->getObj->inbank_code;
        } else return "تم حذف إيداع مالي خارجي";
    }

    public function inbank_status_30()
    {
        if($this->getObj){
            return "'موافقة: إيداع مالي خارجي برقم سند: " . $this->getObj->inbank_code;
        } else return "تم حذف إيداع مالي خارجي";
    }

    public function inbank_status_40()
    {
        if($this->getObj){
            return "'مرفوض: إيداع مالي خارجي برقم سند: " . $this->getObj->inbank_code;
        } else return "تم حذف إيداع مالي خارجي";
    }

    public function notif_html()
    {
        $notefun = $this->notefun;
        return $this->$notefun();
    }

    

}
