@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل صلاحية</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('permissions.index') }}" class="btn btn-primary float-right">كل الصلاحيات</a>
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
                    <h6>تعديل صلاحية: {{ $permission->name }}</h6>
                    <hr>
                    @include('dashboard.includes.alerts.success')
                    @include('dashboard.includes.alerts.errors')
                    <form class="form" action="{{route('permissions.update', $permission->id)}}" method="POST">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $permission->id }}'>

                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$permission, 'name'=>'name', 'transval'=>'اسم الصلاحية', 'maxlength'=>191, 'required'=>'required', 'cols'=>12]) !!}

                            @if(!$roles->isEmpty())
                                <h5 class="col-xs-12 col-lg-12">الرتب <hr> </h5>
                                @foreach ($roles as $role)
                                    <?php $check = (in_array($role->id, $permissionRoleIds))? true : false; ?>
                                    {!! checkbox(['errors'=>$errors, 'value'=>$role->id, 'name'=>'roles[]', 'transval'=>$role->name, 'cols'=>3, 'check'=>$check]) !!}
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

