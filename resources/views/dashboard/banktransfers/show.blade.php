@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="Banktransfer_{{ $banktransfer->id }}">عرض سند تحويل حساب مالي: {{ $banktransfer->banktransfer_code }}</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('banktransfers.index') }}" class="btn btn-primary mx-2 float-right">كل سندات تحويلات الحسابات المالية</a>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- /End Bread crumb and right sidebar toggle Emad test -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title"></h6>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            @if($banktransfer->status->id !=30 && $banktransfer->create_user_id == Auth::id())
                                <a href="{{ route('banktransfers.destroy', [$banktransfer->id]) }}" class="float-right btn btn-danger mx-1">حذف</a>
                            @endif

                            @if($banktransfer->status->id <30 && $banktransfer->create_user_id == Auth::id())
                                <a href="{{ route('banktransfers.edit', [$banktransfer->id]) }}" class="float-right btn btn-warning mx-1">تعديل</a>
                            @endif
                        </div>
                    </div>

                    <div class="row showimage mb-3">
                        <img class="banktransferImage" src="{{ url($banktransfer->image_rel_path()) }}" alt="">
                    </div>

                    <div class="row">
                        {{-- {!! printData(['label'=>'إدخال بواسطة', 'data'=>$banktransfer->create_user->name, 'cols'=>3]) !!} --}}
                        {!! printData(['label'=>'من', 'data'=>$banktransfer->from_bank->bank_name, 'cols'=>3]) !!}
                        {!! printData(['label'=>'إالي', 'data'=>$banktransfer->to_bank->bank_name, 'cols'=>3]) !!}
                        {!! printData(['label'=>'المبلغ', 'data'=>$banktransfer->transfer_amount, 'cols'=>3]) !!}
                        {!! printData(['label'=>'التاريخ', 'data'=>$banktransfer->transfer_date, 'cols'=>3]) !!}
                        {!! printData(['label'=>'الحالة', 'data'=>$banktransfer->status->name, 'cols'=>3]) !!}
                        @if($banktransfer->accept_user)
                        {!! printData(['label'=>'موافقة', 'data'=>$banktransfer->create_user->name, 'cols'=>3]) !!}
                        @endif
                    </div>

                    <div class="row">
                        {!! printData(['label'=>'ملاحظات', 'data'=>$banktransfer->transfer_details, 'cols'=>12]) !!}
                    </div>

                    <div class="row">
                        <div class="col-md-4 my-2">
                        @if(Auth::user()->can(['CRUD_banktransfer']) && $banktransfer->transaction_status_id ==10 )
                        <a class="btn btn-warning" href="{{ route('banktransfers.changestatus', ['banktransfer'=>$banktransfer->id, 'status'=>20]) }}">قيد المراجعة </a>
                        @endif
                        </div>

                        <div class="col-md-4 my-2">
                        @if(Auth::user()->can(['CRUD_banktransfer']) && $banktransfer->transaction_status_id <=20 )
                        <a class="btn btn-success mx-1" href="{{ route('banktransfers.changestatus', ['banktransfer'=>$banktransfer->id, 'status'=>30]) }}">موافقة</a>
                        @endif
                        </div>

                        <div class="col-md-4 my-2">
                        @if(Auth::user()->can(['CRUD_banktransfer']) && $banktransfer->transaction_status_id <=20 )
                        <a class="btn btn-danger mx-1" href="{{ route('banktransfers.changestatus', ['banktransfer'=>$banktransfer->id, 'status'=>40]) }}">رفض</a>
                        @endif
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')

@endsection


