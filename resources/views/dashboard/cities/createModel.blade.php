<form method="POST" action="{{ route('cities.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'label'=>true, 'required'=>'required', 'cols'=>12 ]) !!}
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'r_name', 'transval'=>'اسم المدينة', 'maxlength'=>50, 'required'=>'required', 'cols'=>12]) !!}
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
