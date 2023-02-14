@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل مورد</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('suppliers.index') }}" class="btn btn-primary float-right">كل الموردين</a>
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
                    <h6>تعديل المورد: {{ $supplier->Sup_Name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('suppliers.update', $supplier->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $supplier->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$supplier, 'name'=>'Sup_Name', 'transAttr'=>true, 'maxlength'=>80, 'required'=>'required', 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$supplier, 'name'=>'Sup_phone', 'transAttr'=>true, 'maxlength'=>11, 'required'=>'required', 'attr'=>'maxlength="11" minlength="11"', 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$supplier, 'name'=>'Sup_address', 'transAttr'=>true, 'maxlength'=>191, 'required'=>'required', 'cols'=>4]) !!}
                        </div>

                        <div class="row">
                            {!! checkbox(['errors'=>$errors, 'edit'=>$supplier, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
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
@endsection

