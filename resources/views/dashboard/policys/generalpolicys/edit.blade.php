@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">إعدادات سياسات البيع العامة (الإفتراضية)</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">

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
                    <form method="POST" action="{{ route('generalpolicys.update', $generalpolicy->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $generalpolicy->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$generalpolicy, 'type'=>'time', 'name'=>'last_time', 'transAttr'=>true, 'required'=>'required', 'cols'=>4, 'class'=>' datetimeformat']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$generalpolicy, 'type'=>'number', 'name'=>'rep_limit', 'transAttr'=>true, 'required'=>'required', 'cols'=>4, 'attr'=>'min="0" max="1000000" step="0.01"',]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$generalpolicy, 'type'=>'number', 'name'=>'client_due_limit', 'transAttr'=>true, 'required'=>'required', 'cols'=>4, 'attr'=>'min="0" max="1000000" step="0.01"',]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$generalpolicy, 'type'=>'text', 'name'=>'paid_discount', 'transAttr'=>true, 'required'=>'required', 'cols'=>4, 'attr'=>'min="0" max="100" step="0.01"',]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$generalpolicy, 'type'=>'text', 'name'=>'due_discount', 'transAttr'=>true, 'required'=>'required', 'cols'=>4, 'attr'=>'min="0" max="100" step="0.01"',]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$generalpolicy, 'type'=>'text', 'name'=>'auto_accept_permission_name', 'transAttr'=>true, 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$generalpolicy, 'type'=>'text', 'name'=>'max_return_period', 'transval'=>'اقل فترة بالأيام لاعاده تنزيل المرتجع', 'cols'=>4]) !!}
                            {!! checkbox(['errors'=>$errors, 'edit'=>$generalpolicy, 'name'=>'is_multi_due', 'transAttr'=>true, 'cols'=>3, 'class'=>'switcher']) !!}
                            {!! checkbox(['errors'=>$errors, 'edit'=>$generalpolicy, 'name'=>'pay_more_invoice_balance', 'transval'=>'يمكن للعميل دقع أكبر من قيمة الفاتورة', 'cols'=>3, 'class'=>'switcher']) !!}

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
    $('document').ready(function(){

    });
</script>
@endsection

