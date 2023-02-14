@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="Bank_{{ $bank->id }}">عرض  حساب مالي</h4>
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
                    <h6>عرض ال حساب مالي: {{ $bank->bank_name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('banks.update', $bank->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $bank->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')


                        <div class="row">
                            {!! printData(['label'=>'الحساب', 'data'=>$bank->bank_name, 'cols'=>3]) !!}
                            {!! printData(['transAttr'=>'start_balance', 'data'=>$bank->start_balance, 'cols'=>3]) !!}
                            {!! printData(['transAttr'=>'spend_amounts', 'data'=>$bank->view_bank->spend_amounts, 'cols'=>3]) !!}
                            {!! printData(['transAttr'=>'from_bank_transfer_amount', 'data'=>$bank->view_bank->from_bank_transfer_amount, 'cols'=>3]) !!}
                            {!! printData(['transAttr'=>'to_bank_transfer_amount', 'data'=>$bank->view_bank->to_bank_transfer_amount, 'cols'=>3]) !!}
                            {!! printData(['transAttr'=>'bank_amounts_net', 'data'=>$bank->view_bank->bank_amounts_net, 'cols'=>3]) !!}
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

</script>
@endsection

