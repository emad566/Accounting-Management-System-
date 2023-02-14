@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل سياسة البيع</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('productsales.index') }}" class="btn btn-primary float-right">كل سياسات البيع</a>
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
                    <h6> تعديل سياسة البيع للصنف: {{ $product->Product_Name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('productsales.update', $product->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $product->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$product, 'type'=>'text', 'name'=>'Product_code', 'transval'=>'كود الصنف', 'maxlength'=>50, 'required'=>'required', 'cols'=>6, 'attr'=>'disabled']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$product, 'type'=>'text', 'name'=>'Product_Name', 'transval'=>'اسم الصنف', 'maxlength'=>100, 'required'=>'required', 'cols'=>6, 'attr'=>'disabled']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$product, 'type'=>'number', 'name'=>'Public_Price', 'transAttr'=>true, 'required'=>'required', 'cols'=>12, 'attr'=>'step="0.01" min="0" max="100"', 'attr'=>'disabled']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$product, 'type'=>'number', 'name'=>'Min_Discount', 'transAttr'=>true, 'required'=>'required', 'cols'=>12, 'attr'=>'step="0.01" min="0" max="100"']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$product, 'type'=>'number', 'name'=>'Max_Discount', 'transAttr'=>true, 'required'=>'required', 'cols'=>12, 'attr'=>'step="0.01" min="0" max="100"']) !!}
                        </div>


                        <div class="row">
                            {!! checkbox(['errors'=>$errors, 'edit'=>$product, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
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

