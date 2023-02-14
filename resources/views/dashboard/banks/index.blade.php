@extends('dashboard.master', ['datatable'=>1, 'form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">كل الحسابات المالية</h4>
        </div>
        <div class="col-md-7 align-self-center text-right" dir="rtl">
            <div class="d-flex justify-content-end align-items-center">

                <div id="verticalcenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="vcenter">أضف حساب مالي</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body text-left">
                                @php $cols = 12; @endphp
                                @include('dashboard.banks.createModel')
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">{{ trans('main.Close') }}</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <a href="{{ route('banks.create') }}" data-toggle="modal" data-target="#verticalcenter" class="btn btn-info  m-l-15"><i class="fa fa-plus-circle"></i> {{ trans('main.Add New') }}</a>
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
                    <h4 class="card-title">الحسابات المالية</h4>

                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}



                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('banks.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        @php
                            $fields = [
                                // ['id', 'transAttr'=>true],
                                ['bank_name', 'transAttr'=>true],
                                ['start_balance', 'transAttr'=>true],
                                ['view_bank->inbank_amounts', 'transval'=>'إيداعات خارجية'],
                                ['view_bank->transaction_amounts', 'transval'=>'تحصيلات المندوبين'],
                                ['view_bank->spend_amounts', 'transval'=>'مصاريف'],
                                ['view_bank->from_bank_transfer_amount', 'transval'=>'صادر تحويلات'],
                                ['view_bank->to_bank_transfer_amount', 'transval'=>'وارد تحويلات'],
                                ['view_bank->bank_amounts_net', 'transval'=>'صافي'],
                            ];

                            $htmlrows= '<tr>
                                            <td colspan="3">الأجمـــــــــــــالــــي</td>
                                            <td>'.$banks->sum('start_balance').'</td>
                                            <td>'.$viewbanks->sum('inbank_amounts').'</td>
                                            <td>'.$viewbanks->sum('transaction_amounts').'</td>
                                            <td>'.$viewbanks->sum('spend_amounts').'</td>
                                            <td>'.$viewbanks->sum('from_bank_transfer_amount').'</td>
                                            <td>'.$viewbanks->sum('to_bank_transfer_amount').'</td>
                                            <td>'.$viewbanks->sum('bank_amounts_net').'</td>
                                            <td></td>
                                        </tr>';

                        @endphp

                        <div class="table-responsive m-t-40">
                            {!! indexTable([
                                'objs'=>$banks,
                                'table'=>'banks',
                                'title'=>'bank_name',
                                'trans'=>'',
                                'datatable'=>'',
                                'transval'=>' : الحساب المالي',
                                'active'=>false,
                                'indexEdit'=>true,
                                'indexDel'=>true,
                                'isread'=>false,
                                'view'=>true,
                                'vars'=>false,
                                'fields'=>$fields,
                                'htmlrows'=>$htmlrows,
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

@section('script')

@endsection



