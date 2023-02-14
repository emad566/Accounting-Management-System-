@extends('dashboard.master', ['datatable'=>1, 'form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">كل الفواتير</h4>
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
                    <h4 class="text-themecolor">كل الفواتير</h4>

                    <div class="col-md-12 align-self-center text-right" dir="rtl">
                        <div class="d-flex justify-content-end align-items-center">

                        </div>
                    </div>
                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}

                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('invoices.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        @php
                            $fields = [
                                ['invoice_code', 'transAttr'=>true],
                                ['client->client_name', 'transval'=>'العميل'],
                                ['view_invoice->get_paids', 'transval'=>'تحصيل جم'],
                                ['view_invoice->get_nexts', 'transval'=>'باقي جم'],
                                ['status->name', 'transval'=>'حالة الفاتورة'],
                                ['invoice_date', 'transAttr'=>true],
                            ];
                        @endphp

                        <div class="table-responsive m-t-40">
                            {!! indexTable([
                                'objs'=>$invoices,
                                'table'=>'invoices',
                                'title'=>'invoice_code',
                                'trans'=>'',
                                'transval'=>' :  فاتورة بكود ',
                                'active'=>false,
                                'indexEdit'=>false,
                                'indexDel'=>false,
                                'isread'=>false,
                                'view'=>true,
                                'vars'=>false,
                                'fields'=>$fields,
                            ]) !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
</div>

<?php execution_time($start_time); ?>
@endsection
