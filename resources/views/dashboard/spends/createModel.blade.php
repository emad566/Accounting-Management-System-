<form method="POST" action="{{ route('spends.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        <!-- {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'spend_code', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'cols'=>3]) !!} -->

        @if(Auth::user()->can(['main_safer_admin']))
        {!! select(['errors'=>$errors, 'name'=>'bank_id', 'frkName'=>'bank_name', 'rows'=>$banks, 'transval'=>'الحساب المالي', 'label'=>true, 'cols'=>3 ]) !!}
        @endif

        {!! select(['errors'=>$errors, 'name'=>'cat_id', 'frkName'=>'cat_name', 'rows'=>$cats, 'transval'=>'الفئة', 'label'=>true, 'required'=>'required', 'cols'=>3, 'attrs'=>['is_user'] ]) !!}

        @if(Auth::user()->can(['main_safer_admin']))
        {!! select(['errors'=>$errors, 'name'=>'recieve_user_id', 'frkName'=>'name', 'rows'=>$users, 'transval'=>'العضو المستفيد', 'label'=>true, 'cols'=>3 ]) !!}
        @endif

        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'spend_amount', 'transAttr'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" max="1000000" step="0.01"']) !!}
        {!! input(['errors'=>$errors, 'name'=>'spend_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
    </div>

    <div id="city_idrow" class="row city_idrow">
        {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'selected'=>4, 'label'=>true, 'required'=>'required', 'cols'=>4 ]) !!}
    </div>

    <div>
        {!! input(['errors'=>$errors, 'name'=>'spend_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'cols'=>12]) !!}
    </div>

    <div class="row my-4">
        <p>صورة سند المصروف</p>
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
