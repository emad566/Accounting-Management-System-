<form method="POST" action="{{ route('transactions.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    @if(Auth::user()->usergets)
    <div class="row">
        {!! printData(['label'=>'من رصيد صندوق المندوب الحالي', 'data'=>Auth::user()->usergets->user_safer_balance, 'cols'=>8]) !!}
    </div>
    @endif

    <div class="row">
        <!-- {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'transaction_code', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'cols'=>3]) !!} -->
        {!! select(['errors'=>$errors, 'name'=>'bank_id', 'frkName'=>'bank_name', 'rows'=>$banks, 'transval'=>'إلي حساب', 'label'=>true, 'required'=>'required', 'cols'=>3 ]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'amount', 'transAttr'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" max="1000000000" step="0.01"']) !!}
        {!! input(['errors'=>$errors, 'name'=>'transaction_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
        {!! input(['errors'=>$errors, 'name'=>'transaction_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'cols'=>12]) !!}

        <p>صورة سند التحويل</p>
        <textarea class="hidden" name="imgData" id="imgData"></textarea>
        <input id="image" type="file" accept="image/*" style="display:block"/>
        <div id="previewInvoice" class="invoiceImage">
            <img id="previewinvoiceImg" src="">
        </div>

    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
