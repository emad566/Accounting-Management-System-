<form method="POST" action="{{ route('banks.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'bank_name', 'transAttr'=>true, 'maxlength'=>190, 'required'=>'required', 'cols'=>3]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'start_balance', 'transAttr'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" max="1000000" step="0.01"']) !!}
        {!! input(['errors'=>$errors, 'name'=>'bank_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'تاريخ فتح الحساب', 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
        {!! select(['errors'=>$errors, 'name'=>'create_user_id', 'frkName'=>'fullName', 'rows'=>$users, 'transval'=>'منشئ الحساب', 'label'=>true, 'required'=>'required', 'cols'=>3 ]) !!}
        {!! input(['errors'=>$errors, 'name'=>'bank_details', 'transval'=>'الفاصيل/البيان', 'maxlength'=>191, 'label'=>true, 'cols'=>12]) !!}
    </div>


    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
