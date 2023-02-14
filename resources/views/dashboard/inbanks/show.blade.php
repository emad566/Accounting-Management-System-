@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="inbank_{{ $inbank->id }}">عرض سند إيداع مالي خارجي: {{ $inbank->inbank_code }}</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('inbanks.index') }}" class="btn btn-primary mx-2 float-right">كل سندات الإيداعات المالية الخارجية</a>
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
                            @if($inbank->status->id !=30 && $inbank->create_user_id == Auth::id())
                                <a href="{{ route('inbanks.destroy', [$inbank->id]) }}" class="float-right btn btn-danger mx-1">حذف</a>
                            @endif

                            @if($inbank->status->id <30 && $inbank->create_user_id == Auth::id())
                                <a href="{{ route('inbanks.edit', [$inbank->id]) }}" class="float-right btn btn-warning mx-1">تعديل</a>
                            @endif
                        </div>
                    </div>

                    <div class="row showimage mb-3">
                        <img class="inbankImage" src="{{ url($inbank->image_rel_path()) }}" alt="">
                    </div>

                    <div class="row">
                        {{-- {!! printData(['label'=>'إدخال بواسطة', 'data'=>$inbank->create_user->name, 'cols'=>3]) !!} --}}
                        {!! printData(['label'=>'إالي', 'data'=>$inbank->to_bank->bank_name, 'cols'=>3]) !!}
                        {!! printData(['label'=>'المبلغ', 'data'=>$inbank->inbank_amount, 'cols'=>3]) !!}
                        {!! printData(['label'=>'التاريخ', 'data'=>$inbank->inbank_date, 'cols'=>3]) !!}
                        {!! printData(['label'=>'الحالة', 'data'=>$inbank->status->name, 'cols'=>3]) !!}
                        @if($inbank->accept_user)
                        {!! printData(['label'=>'موافقة', 'data'=>$inbank->create_user->name, 'cols'=>3]) !!}
                        @endif
                    </div>

                    <div class="row">
                        {!! printData(['label'=>'ملاحظات', 'data'=>$inbank->inbank_details, 'cols'=>12]) !!}
                    </div>

                    <div class="row">
                        <div class="col-md-4 my-2">
                        @if(Auth::user()->can(['CRUD_inbank']) && $inbank->transaction_status_id ==10 )
                        <a class="btn btn-warning" href="{{ route('inbanks.changestatus', ['inbank'=>$inbank->id, 'status'=>20]) }}">قيد المراجعة </a>
                        @endif
                        </div>

                        <div class="col-md-4 my-2">
                        @if(Auth::user()->can(['CRUD_inbank']) && $inbank->transaction_status_id <=20 )
                        <a class="btn btn-success mx-1" href="{{ route('inbanks.changestatus', ['inbank'=>$inbank->id, 'status'=>30]) }}">موافقة</a>
                        @endif
                        </div>

                        <div class="col-md-4 my-2">
                        @if(Auth::user()->can(['CRUD_inbank']) && $inbank->transaction_status_id <=20 )
                        <a class="btn btn-danger mx-1" href="{{ route('inbanks.changestatus', ['inbank'=>$inbank->id, 'status'=>40]) }}">رفض</a>
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


