@extends('dashboard.master')

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="form-section"><i class="ft-home"></i> {{ trans('main.resetpass') }} </h4>
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
                    <h6>{{ trans('main.resetpass') }}</h6>
                    <hr>
                    @include('dashboard.includes.alerts.success')
                    @include('dashboard.includes.alerts.errors')
                    <form class="form" action="{{route('mainUser.updatePass')}}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <input type='hidden' name='_method' value='PUT'>

                        <div class="form-body">

                            <div class="row">
                                {!! input(['errors'=>$errors, 'name'=>'oldpassword', 'type'=>'password', 'trans'=>'oldpassword', 'attr'=>'maxlength="20"   minlength="1"', 'required'=>'required', 'cols'=>12 ]) !!}
                            </div>

                            <div class="row">
                                {!! input(['errors'=>$errors, 'name'=>'password', 'type'=>'password', 'trans'=>'newpassword', 'attr'=>'maxlength="20" minlength="6"', 'required'=>'required', 'cols'=>6 ]) !!}

                                {!! input(['errors'=>$errors, 'name'=>'password_confirmation', 'type'=>'password', 'trans'=>'password_confirmation', 'attr'=>'maxlength="20" minlength="6"', 'required'=>'required', 'cols'=>6 ]) !!}
                            </div>



                        </div>
                        {!! buttonAction() !!}
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

