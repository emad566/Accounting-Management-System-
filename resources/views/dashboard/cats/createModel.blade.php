
<form method="POST" action="{{ route('cats.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'cat_name', 'transAttr'=>true, 'maxlength'=>190, 'required'=>'required', 'cols'=>6]) !!}
        {!! checkbox(['errors'=>$errors, 'name'=>'is_user', 'transval'=>'تمكين اختيار عضو', 'cols'=>12, 'class'=>'switcher', 'check'=>false]) !!}
    </div>


    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
