@extends('dashboard.master')

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">{{ trans('main.Profile') }}</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('mainUser.changePass') }}" class="btn btn-primary float-right">{{ trans('main.resetpass') }}</a>
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
                    <h6>{{ trans('main.Profile') }}</h6>
                    <hr>
                    @include('dashboard.includes.alerts.success')
                    @include('dashboard.includes.alerts.errors')
                    <form class="form" action="{{route('mainUser.update')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ Auth::id() }}'>

                        @if($user->usergets)
                        <div class="row">
                            {!! printData(['label'=>'الخزينة النقدية', 'data'=>$user->usergets->user_safer_balance, 'cols'=>12, 'id'=>'', 'class'=>'', ]) !!}
                        </div>
                        @endif

                        <div class="row">
                            {!! img(['errors'=>$errors, 'name'=>'image', 'edit'=>$user, 'trans'=>'userimg', 'cols'=>12]) !!}
                        </div>
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'name'=>'name', 'transval'=>'اسم المستخدم (المعرف)', 'maxlength'=>191, 'required'=>'required', 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'type'=>'phone','name'=>'phone', 'maxlength'=>50, 'transval'=>'رقم الهاتف', 'cols'=>4 ]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'type'=>'email','name'=>'email', 'maxlength'=>50, 'trans'=>'email', 'cols'=>4 ]) !!}
                        </div>
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'name'=>'fName', 'trans'=>'fName', 'maxlength'=>191, 'required'=>'', 'cols'=>6]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'name'=>'lName', 'trans'=>'lName', 'maxlength'=>191, 'required'=>'', 'cols'=>6]) !!}
                        </div>
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'name'=>'dt_font_size', 'transval'=>'حجم الخط للتقارير', 'maxlength'=>191, 'required'=>'', 'cols'=>6]) !!}
                        </div>
                        <div class="row">
                            {!! checkbox(['errors'=>$errors, 'edit'=>$user, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12]) !!}
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

