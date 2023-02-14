@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor"  id="Store_{{ $store->id }}"> مخزن: {{ $store->Stroe_Name }}</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('stores.index') }}" class="btn btn-primary float-right">كل المخازن</a>
                @if(Auth::user()->can(['Cud stores']))
                <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-warning mx-2 float-right">تعديل</a>
                @endif
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


                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$store, 'name'=>'Store_Name', 'transval'=>'اسم المخزن', 'maxlength'=>50, 'required'=>'required', 'cols'=>12, 'attr'=>'disabled']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$store, 'name'=>'Store_Place', 'transval'=>'مكان المخزن', 'maxlength'=>50, 'required'=>'required', 'cols'=>12, 'attr'=>'disabled']) !!}
                        </div>

                        @if($store->id != 1)
                        <div class="row">
                            {!! checkbox(['errors'=>$errors, 'edit'=>$store, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher', 'attr'=>'disabled']) !!}
                        </div>

                        <div class="relationElements">
                            <h5 class="col-xs-12 col-lg-12">مناطق يعمل فيها المخزن<hr> </h5>

                            <div class="col-12 table-responsive">
                                <table id="productsTable" class="table table-hover table-bordered color-bordered-table purple-bordered-table">
                                    <thead>
                                        <th>#</th>
                                        <th>المحافظة</th>
                                        <th>المدينة</th>
                                        <th>المنطقة</th>
                                    </thead>
                                    <tbody>
                                        <?php $i=0;
                                            $regions_ids = $store->regions->pluck('id');
                                        ?>
                                        @if ($regions_ids)
                                            @foreach ($regions_ids as $region)
                                                <?php $region = App\Models\Region::find($region); ?>
                                                @if($region)
                                                    <tr id="rowRegion{{ $region->id }}">
                                                        <td>{{ ++$i }}
                                                            <input type="hidden" name="regions[]" value="{{ $region->id }}" id="region_{{ $region->id }}" class="region_{{ $region->id }} regions">
                                                        </td>
                                                        <td>{{ $region->get_state_name() }}</td>
                                                        <td>{{ $region->get_city_name() }}</td>
                                                        <td>{{ $region->get_region_name() }}</td>
                                                    <tr>
                                                @endif
                                            @endforeach

                                        @endif
                                    </tbody>
                                </table>
                                <input type="hidden" value="{{ $i }}" id="lastCount">
                            </div>
                        </div>


                        <div class="row">
                            @if(!$users->isEmpty())
                            <h5 class="col-xs-12 col-lg-12">مستخدمين لهم صلاحيات علي هذا المخزن <hr> </h5>
                            <?php $i=0; ?>
                                @foreach ($users as $user)
                                    <?php
                                        if(old('users'))
                                            $check = (in_array($user->id ,old('users')) )? true : false;
                                        else
                                            $check = (in_array($user->id ,$store->users->pluck('id')->toArray()) )? true : false;
                                    ?>
                                    {!! checkbox(['errors'=>$errors, 'value'=>$user->id, 'name'=>'users[]', 'transval'=>$user->fullName, 'cols'=>3, 'check'=>$check, 'attr'=>'disabled']) !!}
                                @endforeach
                            @endif
                        </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
@endsection

