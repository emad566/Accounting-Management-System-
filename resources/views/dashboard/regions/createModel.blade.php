<form method="POST" action="{{ route('regions.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div id="city_idrow" class="row city_idrow">
        {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'label'=>true, 'required'=>'required', 'cols'=>12 ]) !!}
    </div>

    <div class="row">
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'r_name', 'transval'=>'اسم المدينة', 'maxlength'=>50, 'required'=>'required', 'cols'=>12]) !!}
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
