@extends('dashboard.master', ['datatable'=>1, 'form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">سياسة البيع</h4>
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
                    <h4 class="card-title">سياسة البيع</h4>

                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}

                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action=''>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        @php
                            $fields = [
                                // ['id', 'transAttr'=>true],
                                ['Product_code', 'transAttr'=>true],
                                ['Product_Name', 'transAttr'=>true],
                                ['Public_Price', 'transAttr'=>true],
                                // ['Min_Discount', 'transAttr'=>true],
                                // ['Max_Discount', 'transAttr'=>true],
                            ];
                        @endphp

                        <div class="table-responsive m-t-40">
                            {!! indexTable([
                                'objs'=>$products,
                                'table'=>'productsales',
                                'title'=>'product_Name',
                                'trans'=>'',
                                'transval'=>' :الصنف',
                                'active'=>false,
                                'indexEdit'=>true,
                                'indexDel'=>false,
                                'isread'=>false,
                                'view'=>false,
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

@section('script')
    <script>

    </script>
@endsection

