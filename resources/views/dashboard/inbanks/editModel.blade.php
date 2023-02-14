<form method="POST" action="{{ route('inbanks.update', $inbank->id) }}" class="form-horizontal form-material" id="loginform">
    @csrf
    <input type='hidden' name='_method' value='PUT'>
    <input type='hidden' name='id' value='{{ $inbank->id }}'>

    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    <div class="row">
        <!-- {!! input(['errors'=>$errors, 'edit'=>$inbank, 'name'=>'inbank_code', 'type'=>'text', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!} -->
        {!! select(['errors'=>$errors, 'edit'=>$inbank, 'name'=>'to_bank_id', 'frkName'=>'bank_name', 'rows'=>$banks, 'transval'=>'إالي', 'label'=>true, 'required'=>'required', 'cols'=>3, 'select_id'=>$inbank->to_bank->id ]) !!}
        {!! input(['errors'=>$errors, 'edit'=>$inbank, 'type'=>'number', 'name'=>'inbank_amount', 'transAttr'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" max="1000000" step="0.01"']) !!}
        {!! input(['errors'=>$errors, 'edit'=>$inbank, 'name'=>'inbank_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
        {!! input(['errors'=>$errors, 'edit'=>$inbank, 'name'=>'inbank_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'cols'=>12]) !!}
    </div>

    <div class="row my-4">
        <p>صورة سند التحويل المالي</p>
        <textarea class="hidden" name="imgData" id="imgData"></textarea>
        <input id="image" type="file" accept="image/*" style="display:block"/>
        <div id="previewInvoice" class="invoiceImage">
            <img id="previewinvoiceImg" src="{{ url($inbank->image_rel_path()) }}">
        </div>
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
