@extends('dashboard.master', ['ishome'=>1, 'dashboard1'=>1, 'charts'=>1, 'morris'=>1, 'toast'=>1])

@section('content') 
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- page-title Bread crumb and right sidebar toggle                -->
    <!-- ============================================================== -->

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Home</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Marvel</li>
                </ol>
                {{-- <button type="button" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Create New</button> --}}
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End page-title Bread crumb and right sidebar toggle            -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content                                              -->
    <!-- =============================================================== -->
    <div class="row">
        <div class="col-12">
            {{-- <img style="margin: auto; display: block; width: 100%;" loading="lazy" src="{{ asset('images/Chart.jpg') }}" alt=""> --}}
            <div class="card">
                <div class="card-body">
                    @include('dashboard.includes.alerts.success')
                    @include('dashboard.includes.alerts.errors')
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