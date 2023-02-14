<form method="POST" action="{{ route('states.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    
    <div class="row">
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'r_name', 'transval'=>'اسم المحافظة', 'maxlength'=>50, 'required'=>'required', 'cols'=>12]) !!}
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
