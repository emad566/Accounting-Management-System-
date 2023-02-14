@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل  حساب مالي</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('banks.index') }}" class="btn btn-primary float-right">كل الحسابات  المالية</a>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- /End Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6>تعديل ال حساب مالي: {{ $bank->bank_name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('banks.update', $bank->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $bank->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')


                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$bank, 'type'=>'text', 'name'=>'bank_name', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$bank, 'type'=>'number', 'name'=>'start_balance', 'transAttr'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" max="1000000" step="0.01"']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$bank, 'name'=>'bank_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'تاريخ فتح الحساب', 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
                            {!! select(['errors'=>$errors, 'edit'=>$bank, 'name'=>'create_user_id', 'frkName'=>'fullName', 'rows'=>$users, 'transval'=>'منشئ الحساب', 'label'=>true, 'required'=>'required', 'cols'=>3, 'select_id'=>$bank->creat_user_id ]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$bank, 'name'=>'bank_details', 'transval'=>'الفاصيل/البيان', 'maxlength'=>191, 'label'=>true, 'cols'=>12]) !!}
                        </div>

                        <div class="row">
                            {!! buttonAction() !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
<script>

</script>
@endsection

