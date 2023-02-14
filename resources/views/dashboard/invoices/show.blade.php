@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h4 class="text-themecolor" id="Invoice_{{ $invoice->id }}">فاتورة عميل رقم: {{ $invoice->invoice_code }}
                    ({{ $invoice->status->name }}) </h4>
            </div>
            <div class="col-md-7 align-self-center text-right">
                <div class="d-flex justify-content-end align-items-center">
                    <a href="{{ route('invoices.index') }}" class="btn btn-primary mx-2 float-right">
                        كل الفواتير </a>
                    <a href="{{ route('vouchers.show', $invoice->voucher->id) }}"
                        class="btn btn-primary mx-2  float-right">إذن صرف رقم: {{ $invoice->voucher->voucher_code }} </a>

                    @if (Auth::user()->can('Accounting'))
                        <a href="{{ route('clients.accounting', $invoice->client_id) }}"
                            class="btn btn-primary mx-2  float-right">عرض كشف حساب العميل:
                            {{ $invoice->client->client_name }}</a>
                    @endif

                    @if (Auth::user()->voucher && Auth::user()->can('Create Invoices') && !Auth::user()->voucher->user_keeper_return_id && !Auth::user()->voucher->user_accountant_return_id && Auth::user()->voucher->voucher_status == 3)
                        <a href="{{ route('invoices.create', Auth::user()->voucher->id) }}"
                            class="btn btn-info mx-2 float-right">أضف جديد</a>
                    @endif
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- /End Bread crumb and right sidebar toggle Emad test -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        @include('dashboard.invoices.showModel')
    </div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
<?php execution_time($start_time); ?>
@endsection
