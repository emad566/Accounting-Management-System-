@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="Transfer_{{ $transfer->id }}">  أمر تحويل رقم: {{ $transfer->transfer_code }}</h4>
        </div>
        
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('transfers.index') }}" class="btn btn-primary float-right">كل أوامر التحويل</a>
                @if (Auth::user()->can(['CRUD Transfers']) && $transfer->transfer_status_id ==10)
                    <a href="{{ route('transfers.edit', $transfer->id) }}" class="btn btn-warning float-right mx-2">تعديل</a>
                    <a href="{{ route('transfers.destroy', $transfer->id) }}" class="btn btn-danger float-right mx-2">حذف</a>
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
                    <h6 class="card-title"></h6>
                <hr>
                    @include('dashboard.includes.alerts.success')
                    @include('dashboard.includes.alerts.errors')
                    <div class="row">
                        {!! printData(['transAttr'=>'from_store_id', 'data'=>$transfer->storeFrom->Store_Name, 'cols'=>4]) !!}
                        {!! printData(['transAttr'=>'to_store_id', 'data'=>$transfer->storeTo->Store_Name, 'cols'=>4]) !!}
                        {!! printData(['transAttr'=>'transfer_code', 'data'=>$transfer->transfer_code, 'cols'=>4]) !!}
                        {!! printData(['transAttr'=>'transfer_name', 'data'=>$transfer->transfer_name, 'cols'=>4]) !!}
                        {!! printData(['transAttr'=>'transfer_phone', 'data'=>$transfer->transfer_phone, 'cols'=>4]) !!}
                        {!! printData(['transAttr'=>'transfer_status', 'data'=>$transfer->status->name, 'cols'=>4]) !!}
                        {!! printData(['transAttr'=>'transfer_date', 'data'=>$transfer->transfer_date, 'cols'=>4]) !!}
                        {!! printData(['transAttr'=>'transfer_details', 'data'=>$transfer->transfer_details, 'cols'=>8]) !!}
                    </div>

                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                                <thead>
                                    <th>#</th>
                                    <th>الصنف</th>
                                    <th>{{ trans('validation.attributes.runID') }}</th>
                                    <th>الكمية</th>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=0;
                                    ?>
                                    @foreach ($transfer->products as $product)
                                    <tr id="row{{ $product->id }}">
                                        <td>{{ ++$i }}</td>
                                        <td>{{ App\Models\Product::findOrFail($product->id)->Product_Name }}</td>
                                        <td>{{ $product->pivot->RunID }}</td>
                                        <td>{{ $product->pivot->Quantity }}</td>
                                    <tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <input type="hidden" value="{{ $i }}" id="lastCount">
                        </div>
                    </div>

                    <div id="linkgroup" class="row">
                        @if(Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 10 && $canchangestatusTo2030)
                        <a class="btn btn-warning mx-2" href="{{ route('transfers.changestatus', ['transfer'=>$transfer->id, 'status_id'=>20]) }}">تغيير الي قيد الشحن</a>
                        @endif

                        @if(Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 20 && $canchangestatusTo2030)
                        <a class="btn btn-danger mx-2" href="{{ route('transfers.changestatus', ['transfer'=>$transfer->id, 'status_id'=>30]) }}">إلغاء/رفض استلام</a>
                        @endif

                        @if(Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 20 && $canchangestatusTo40)
                        <a class="btn btn-success mx-2" href="{{ route('transfers.changestatus', ['transfer'=>$transfer->id, 'status_id'=>40]) }}">استلام امر التحويل</a>
                        @endif

                        @if($transfer->status->id == 40 && Auth::user()->can('Transfer_Back_Again'))
                        <a class="btn btn-success mx-2" href="{{ route('transfers.changestatus', ['transfer'=>$transfer->id, 'status_id'=>-20]) }}">إرجع للشحن  مرة أخري</a>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection
@section('script')
<script>
    $(document).ready(function(){
        $("#linkgroup a").on('click', function(){
            $("#linkgroup").html("<p style='font-size:25px; text-align:left; color:blue; padding:20px'>الرجاء الانتظار جاري حفظ التعديلات ..... </p>")
        })
    })
</script>
@endsection


