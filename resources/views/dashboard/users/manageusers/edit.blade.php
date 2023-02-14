@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل عضو</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('manageusers.index') }}" class="btn btn-primary float-right">كل الأعضاء</a>
                <a href="{{ route('manageusers.changePass', $user->id) }}" class="btn btn-success float-right mx-1 ">تعديل كلمة المرور</a>
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
                    <h6>تعديل العضو: {{ $user->name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('manageusers.update', $user->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $user->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'name'=>'name', 'transval'=>'اسم المستخدم', 'maxlength'=>20, 'required'=>'required', 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'name'=>'email', 'transval'=>'البريد الإلكتروني', 'maxlength'=>50, 'required'=>'required', 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'type'=>'phone','name'=>'phone', 'maxlength'=>50, 'transval'=>'رقم الهاتف', 'cols'=>4 ]) !!}

                            {!! input(['errors'=>$errors, 'edit'=>$user, 'name'=>'fName', 'transval'=>'الأسم الأول', 'maxlength'=>30, 'required'=>'required', 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$user, 'name'=>'lName', 'transval'=>'باقي الأسم', 'maxlength'=>100, 'required'=>'required', 'cols'=>4]) !!}
                        </div>

                        <div class="row">
                            {!! select(['errors'=>$errors, 'name'=>'role_id', 'frkName'=>'name', 'rows'=>$roles, 'transval'=>'الرتبة', 'label'=>false, 'required'=>'required', 'cols'=>4, 'select_id'=>$select_id ]) !!}
                        </div>

                        <div class="row">
                            {!! checkbox(['errors'=>$errors, 'edit'=>$user, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
                        </div>

                        <div class="row">
                            @if(!$stores->isEmpty())
                                <h5 class="col-xs-12 col-lg-12">المخازن التي له صلاحية عليها<hr> </h5>
                                <?php $i=0; ?>
                                @foreach ($stores as $store)
                                    <?php
                                        if(old('stores'))
                                            $check = (in_array($store->id ,old('stores')) )? true : false;
                                        else
                                            $check = (in_array($store->id ,$user->stores->pluck('id')->toArray()) )? true : false;
                                    ?>
                                    {!! checkbox(['errors'=>$errors, 'value'=>$store->id, 'name'=>'stores[]', 'transval'=>$store->Store_Name, 'cols'=>3, 'check'=>$check]) !!}
                                @endforeach
                            @endif
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

