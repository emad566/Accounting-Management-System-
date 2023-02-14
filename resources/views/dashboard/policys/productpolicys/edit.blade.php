@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor"> تعديل صنف سياسات</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('productpolicys.index') }}" class="btn btn-primary float-right">كل سياسات الأصناف</a>
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
                    <h6> تعديل سياسات الصنف: {{ $productpolicy->Product_Name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('productpolicys.update', $productpolicy->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $productpolicy->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$productpolicy, 'type'=>'text', 'name'=>'Product_code', 'transval'=>'كود الصنف', 'maxlength'=>50, 'required'=>'required', 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$productpolicy, 'type'=>'text', 'name'=>'Product_Name', 'transval'=>'اسم الصنف', 'maxlength'=>100, 'required'=>'required', 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$productpolicy, 'type'=>'number', 'name'=>'Public_Price', 'transAttr'=>true, 'maxlength'=>9999999, 'required'=>'required', 'cols'=>4, 'attr'=>'step="0.01" min="0" max="1000"']) !!}

                            {!! input(['errors'=>$errors, 'edit'=>$productpolicy, 'type'=>'text', 'name'=>'paid_discount', 'transAttr'=>true, 'cols'=>4, 'attr'=>'min="0" max="100" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من سياسات المنطقة']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$productpolicy, 'type'=>'text', 'name'=>'due_discount', 'transAttr'=>true, 'cols'=>4, 'attr'=>'min="0" max="100" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من سياسات المنطقة']) !!}
                            {!! select(['errors'=>$errors,'edit'=>$productpolicy, 'name'=>'is_multi_due_inherit_id', 'frkName'=>'name', 'rows'=>$isinherits, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>4]) !!}

                        </div>


                        <div class="row">
                            {!! checkbox(['errors'=>$errors, 'edit'=>$productpolicy, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
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

