<form method="POST" action="{{ route('suppliers.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        {!! input(['errors'=>$errors, 'name'=>'Sup_Name', 'transAttr'=>true, 'maxlength'=>80, 'required'=>'required', 'cols'=>4]) !!}
        {!! input(['errors'=>$errors, 'name'=>'Sup_phone', 'transAttr'=>true, 'required'=>'required', 'attr'=>'maxlength="11" minlength="11"', 'cols'=>4]) !!}
        {!! input(['errors'=>$errors, 'name'=>'Sup_address', 'transAttr'=>true, 'maxlength'=>191, 'required'=>'required', 'cols'=>4]) !!}
    </div>

    <div class="row">
        {!! checkbox(['errors'=>$errors, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
