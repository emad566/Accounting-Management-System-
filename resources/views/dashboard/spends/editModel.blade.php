<form method="POST" action="{{ route('spends.update', $spend->id) }}" class="form-horizontal form-material" id="loginform">
    @csrf
    <input type='hidden' name='_method' value='PUT'>
    <input type='hidden' name='id' value='{{ $spend->id }}'>

    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        <!-- {!! input(['errors'=>$errors, 'edit'=>$spend, 'type'=>'text', 'name'=>'spend_code', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'cols'=>3]) !!} -->

        @if(Auth::user()->can(['main_safer_admin']))
        {!! select(['errors'=>$errors, 'edit'=>$spend, 'name'=>'bank_id', 'frkName'=>'bank_name', 'rows'=>$banks, 'transval'=>'الحساب المالي', 'label'=>true, 'cols'=>3, 'select_id'=>$spend->bank_id ]) !!}
        @endif

        {!! select(['errors'=>$errors, 'name'=>'cat_id', 'frkName'=>'cat_name', 'rows'=>$cats, 'transval'=>'الحساب المالي', 'label'=>true, 'required'=>'required', 'cols'=>4, 'select_id'=>$spend->cat_id ]) !!}

        @if(Auth::user()->can(['main_safer_admin']))
        {!! select(['errors'=>$errors, 'edit'=>$spend, 'name'=>'recieve_user_id', 'frkName'=>'name', 'rows'=>$users, 'transval'=>'العضو المستفيد', 'label'=>true, 'cols'=>3, 'select_id'=>$spend->recieve_user_id ]) !!}
        @endif

        {!! input(['errors'=>$errors, 'edit'=>$spend, 'type'=>'number', 'name'=>'spend_amount', 'transAttr'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" max="1000000" step="0.01"']) !!}
        {!! input(['errors'=>$errors, 'edit'=>$spend, 'name'=>'spend_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
    </div>

    <div id="city_idrow" class="row city_idrow">
        {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'selected'=>4, 'label'=>true, 'required'=>'required', 'cols'=>4 ]) !!}
    </div>

    <div>
        {!! input(['errors'=>$errors, 'edit'=>$spend, 'name'=>'spend_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'cols'=>12]) !!}
    </div>

    <div class="row my-4">
        <p>صورة سند الصرف</p>
        <textarea class="hidden" name="imgData" id="imgData"></textarea>
        <input id="image" type="file" accept="image/*" style="display:block"/>
        <div id="previewInvoice" class="invoiceImage">
            <img id="previewinvoiceImg" src="{{ url($spend->image_rel_path()) }}">
        </div>
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
