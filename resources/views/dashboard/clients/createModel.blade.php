<form method="POST" action="{{ route('clients.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'client_name', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'cols'=>6]) !!}
        {!! select(['errors'=>$errors, 'name'=>'client_type_id', 'frkName'=>'name', 'rows'=>$client_types, 'transval'=>'النوع', 'label'=>true, 'required'=>'required', 'cols'=>6 ]) !!}
    </div>

    <div id="city_idrow" class="row city_idrow">
        {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'label'=>true, 'required'=>'required', 'cols'=>12 ]) !!}
    </div>

    <div id="region_idrow" class="row region_idrow"></div>

    <div class="row">
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'client_address', 'transAttr'=>true, 'maxlength'=>100, 'cols'=>3]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'client_phone', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="111" max="99999999999"']) !!}
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'client_manager_name', 'transAttr'=>true, 'maxlength'=>50, 'cols'=>3]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'client_manager_phone', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="111" max="99999999999"']) !!}
    </div>


    <div class="row">
        @if(Auth::user()->can(['Delete_Client']))
            {!! input(['errors'=>$errors, 'type'=>'', 'name'=>'initial_balance', 'transval'=>'رصيد إفتتاحي', 'maxlength'=>50, 'cols'=>3]) !!}
        @endif
        {!! checkbox(['errors'=>$errors, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
