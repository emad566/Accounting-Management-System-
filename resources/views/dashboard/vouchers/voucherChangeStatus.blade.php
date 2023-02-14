
@if(@$notification)
    <p>{{ $notification['message'] }}</p>
@endif


@if(Auth::user()->can('Accept Or Refuse Vouchers') && $voucher->voucher_status == 1)
    <a href="{{ route('vouchers.accept', $voucher->id) }}" class="btn btn-success btn-sm float-left mx-1 my-1">إضغط هنا للموافقة علي الصرف</a>
    <a href="{{ route('vouchers.refuse', $voucher->id) }}" class="btn btn-danger btn-sm float-left mx-1 my-1">إضغط هنا لرفض الصرف</a>
@endif

@if(Auth::user()->can('Keeper Out Accept') && $voucher->voucher_status == 2 && $voucher->user_rep_id == Auth::id())
    <a href="{{ route('vouchers.keeperaccept', $voucher->id) }}" class="btn btn-warning btn-sm float-left mx-1 my-1">إضغط هنا  لتأكيد خروج الإذن من المخزن</a>
@endif

@if(Auth::user()->can('settlement_request') && $voucher->voucher_status == 3  && $canRequire && $voucher->user_rep_id == Auth::id())
    <a href="{{ route('vouchers.settlement_request', $voucher->id) }}" class="btn btn-warning btn-sm float-left mx-1 my-1">إضغط هنا  لطلب التسوية</a>
@elseif(Auth::user()->can('Review Accountant Vouchers') && $voucher->voucher_status == 3)
<b style="font-size: 10px;" class="btn btn-warning btn-sm float-left mx-1 my-1">لم يقم المندوب بطلب التسوية حتي الأن</b>
@elseif($voucher->voucher_status==3)
    <b style="font-size: 10px;" class="btn btn-warning btn-sm float-left mx-1 my-1">يجب موافقة المحاسب علي كل الفواتير حتي تتمكن من تسوية إذن الصرف</b>
@endif

@if(!$voucher->user_accountant_return_id && $voucher->voucher_status == 6 && !$voucher->user_keeper_return_id)
    @if((Auth::user()->can('Review_Accountant_his_Vouchers') && Auth::user()->voucher_id == $voucher->id) || Auth::user()->can('Review Accountant Vouchers'))
    <a href="{{ route('vouchers.accountantreturn', $voucher->id) }}" class="btn btn-success btn-sm float-left mx-1 my-1">إضغط هنا  لتأكيد مراجعة  وتسوية المحاسب</a>
    @endif
@endif

@if(Auth::user()->can('Review Keeper Vouchers') && !$voucher->user_keeper_return_id && $voucher->voucher_status == 6 && $voucher->user_accountant_return_id)
    <a href="{{ route('vouchers.keeperreturn', $voucher->id) }}" class="btn btn-success btn-sm float-left mx-1 my-1">إضغط هنا  لتأكيد مراجعة  وتسوية أمين المخزن</a>
@endif

