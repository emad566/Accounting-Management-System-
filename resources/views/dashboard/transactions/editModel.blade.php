<form method="POST" action="{{ route('transactions.update', $transaction->id) }}" class="form-horizontal form-material" id="loginform">
    @csrf
    <input type='hidden' name='_method' value='PUT'>
    <input type='hidden' name='id' value='{{ $transaction->id }}'>

    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    <div class="row">
        {!! printData(['label'=>'من رصيد صندوق المندوب الحالي', 'data'=>Auth::user()->usergets->user_safer_balance, 'cols'=>8]) !!}
    </div>

    <div class="row">

        <!-- {!! input(['errors'=>$errors, 'edit'=>$transaction, 'type'=>'text', 'name'=>'transaction_code', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'cols'=>3]) !!} -->
        {!! select(['errors'=>$errors, 'name'=>'bank_id', 'frkName'=>'bank_name', 'rows'=>$banks, 'transval'=>'إلي حساب', 'label'=>true, 'required'=>'required', 'cols'=>3, 'select_id'=>$transaction->bank_id ]) !!}
        {!! input(['errors'=>$errors, 'edit'=>$transaction, 'type'=>'number', 'name'=>'amount', 'transAttr'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" max="1000000" step="0.01"']) !!}
        {!! input(['errors'=>$errors, 'edit'=>$transaction, 'name'=>'transaction_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
        {!! input(['errors'=>$errors, 'edit'=>$transaction, 'name'=>'transaction_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'cols'=>12]) !!}

        <p>صورة سند التحويل</p>
        <textarea class="hidden" name="imgData" id="imgData"></textarea>
        <input id="image" type="file" accept="image/*" style="display:block"/>
        <div id="previewInvoice" class="invoiceImage">
            <img id="previewinvoiceImg" src="{{ url($transaction->image_rel_path()) }}">
        </div>

    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
