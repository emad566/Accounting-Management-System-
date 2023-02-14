@if(Auth::user()->usergets)
<li class="nav-item">
    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="{{ route('reports.usergets', Auth::user()->id) }}" title="صندوق المندوب">

            <span style="color: #fff; font-size: 10px">{{ Auth::user()->usergets->user_safer_balance }}</span>
    </a>
</li>
@endif

<li class="nav-item">
    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="{{ route('gets.newget') }}" title="تحصيل">
        <i class="fas fa-hand-holding-usd" style="color: green;"></i>
        <div class="notify notifications">
            <span class="heartbit"></span>
            <span class="point"></span>
        </div>
    </a>
</li>




@if(Auth::user()->voucher && Auth::user()->can('Create Invoices') && !Auth::user()->voucher->user_keeper_return_id && !Auth::user()->voucher->user_accountant_return_id && Auth::user()->voucher->voucher_status == 3)
<li class="nav-item">
    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="{{ route('invoices.create', Auth::user()->voucher->id) }}" title="إنشاء فاتورة">
        <i class="fa fa-file-invoice" style="color: yellow;"></i>
        <div class="notify notifications">
            <span class="heartbit"></span>
            <span class="point"></span>
        </div>
    </a>
</li>
@endif

@if(Auth::user()->voucher_id)
<li class="nav-item">
    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="{{ route('vouchers.show', Auth::user()->voucher->id) }}" title="إذن صرف مفتوح">
        <!-- <i class="fa fa-file-invoice" style="color: yellow;"></i> -->
        <i class="fas fa-suitcase" style="color: black;"></i>
        <div class="notify notifications">
            <span class="heartbit"></span>
            <span class="point"></span>
        </div>
    </a>
</li>
@endif


