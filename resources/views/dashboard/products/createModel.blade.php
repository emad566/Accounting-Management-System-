<form method="POST" action="{{ route('products.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'Product_code', 'transval'=>'كود الصنف', 'maxlength'=>50, 'required'=>'required', 'cols'=>4]) !!}
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'Product_Name', 'transval'=>'اسم الصنف', 'maxlength'=>100, 'required'=>'required', 'cols'=>4]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'Public_Price', 'transAttr'=>true, 'maxlength'=>9999999, 'required'=>'required', 'cols'=>4, 'attr'=>'step="0.01" min="0" max="1000"']) !!}
    </div>

    <div class="row">
        {!! checkbox(['errors'=>$errors, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
