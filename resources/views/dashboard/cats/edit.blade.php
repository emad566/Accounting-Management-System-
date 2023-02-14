@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل فئة</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('cats.index') }}" class="btn btn-primary float-right">كل الفئات</a>
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
                    <h6>تعديل الفئة: {{ $cat->cat_name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('cats.update', $cat->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $cat->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')


                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$cat, 'type'=>'text', 'name'=>'cat_name', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'cols'=>6]) !!}
                            {!! checkbox(['errors'=>$errors, 'edit'=>$cat, 'name'=>'is_user', 'transval'=>'تمكين اختيار عضو', 'cols'=>12, 'class'=>'switcher']) !!}</div>
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

