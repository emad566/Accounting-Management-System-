@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor"> التحويل بين المخازن: إذن تحويل</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('transfers.index') }}" class="btn btn-primary float-right">كل أوامر التحويل بين المخازن </a>
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
                    <hr>
                    <form method="POST" action="{{ route('transfers.create') }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <div class="row">
                            {!! select(['errors'=>$errors, 'name'=>'from_store_id', 'frkName'=>'Store_Name', 'rows'=>$stores, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>6]) !!}
                            {!! select(['errors'=>$errors, 'name'=>'to_store_id', 'frkName'=>'Store_Name', 'rows'=>$stores, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>6]) !!}
                        </div>

                        <div class="row">
                            {!! buttonAction('next') !!}
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
        $(document).on("click", "#transferAdd", function(e){

        });
    })
</script>
@endsection


