<form method="POST" action="{{ route('banktransfers.update', $banktransfer->id) }}" class="form-horizontal form-material" id="loginform">
    @csrf
    <input type='hidden' name='_method' value='PUT'>
    <input type='hidden' name='id' value='{{ $banktransfer->id }}'>

    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        <!-- {!! input(['errors'=>$errors, 'edit'=>$banktransfer, 'name'=>'banktransfer_code', 'type'=>'text', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!} -->
        {!! select(['errors'=>$errors, 'edit'=>$banktransfer, 'name'=>'from_bank_id', 'frkName'=>'bank_name', 'rows'=>$banks, 'transval'=>'من', 'label'=>true, 'required'=>'required', 'cols'=>3, 'select_id'=>$banktransfer->from_bank->id ]) !!}
        {!! select(['errors'=>$errors, 'edit'=>$banktransfer, 'name'=>'to_bank_id', 'frkName'=>'bank_name', 'rows'=>$banks, 'transval'=>'إالي', 'label'=>true, 'required'=>'required', 'cols'=>3, 'select_id'=>$banktransfer->to_bank->id ]) !!}
        {!! input(['errors'=>$errors, 'edit'=>$banktransfer, 'type'=>'number', 'name'=>'transfer_amount', 'transAttr'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" max="1000000" step="0.01"']) !!}
        {!! input(['errors'=>$errors, 'edit'=>$banktransfer, 'name'=>'transfer_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
        {!! input(['errors'=>$errors, 'edit'=>$banktransfer, 'name'=>'transfer_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'cols'=>12]) !!}
    </div>

    <div class="row my-4">
        <p>صورة سند التحويل المالي</p>
        <textarea class="hidden" name="imgData" id="imgData"></textarea>
        <input id="image" type="file" accept="image/*" style="display:block"/>
        <div id="previewInvoice" class="invoiceImage">
            <img id="previewinvoiceImg" src="{{ url($banktransfer->image_rel_path()) }}">
        </div>
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
