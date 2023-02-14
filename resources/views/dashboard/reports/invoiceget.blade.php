@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'تقرير التحصيل /المديونية'])

@section('content')
<?php $start_time = microtime(true); ?>
<style>
    .toggleColumn a{
        color: blue;
        font-size: 0.7em;
    }
    .actionLinks a{
        margin-right: 10px;
    }
</style>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">التقارير</h4>
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
                    <h6 class="card-title">تقرير المبيعات - التحصيل</h6>
                    <form id="searchForm" class="searchForm">
                        <div class="row">
                            {!! select(['errors'=>$errors, 'name'=>'client_id', 'frkName'=>'client_name_title', 'rows'=>$clients, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}
                            {{-- {!! select(['errors'=>'', 'name'=>'client_id', 'frkName'=>'client_name', 'rows'=>$clients, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'data-live-search="true"']) !!} --}}
                            {!! select(['errors'=>'', 'name'=>'client_type_id', 'frkName'=>'name', 'rows'=>$client_types, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}
                            {!! select(['errors'=>'', 'name'=>'rep_id', 'frkName'=>'fullName', 'rows'=>$reps, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}
                            {!! select(['errors'=>'', 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}
                            {!! select(['errors'=>'', 'name'=>'runID', 'frkName'=>'runID', 'rows'=>[], 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}
                            {!! select(['errors'=>'', 'name'=>'invoice_pay_status_id', 'frkName'=>'name', 'select_id'=>40,  'rows'=>$invoice_pay_status, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}


                        </div>
                        <div id="city_idrow" class="row city_idrow">
                            {!! input(['errors'=>$errors, 'name'=>'report_period', 'type'=>'number', 'transval'=>'فتر ة تقرير بطئ الحركة باليوم', 'label'=>true, 'cols'=>2]) !!}
                            {!! input(['errors'=>$errors, 'name'=>'invoice_date_from', 'type'=>'date', 'value'=>(new Carbon\Carbon('2021-01-01'))->isoFormat('YYYY-MM-DD'), 'transval'=>'من (تاريخ انشاء الفاتورة)', 'label'=>true, 'cols'=>2]) !!}
                            {!! input(['errors'=>$errors, 'name'=>'invoice_date_to', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'إالي (تاريخ انشاء الفاتورة)', 'label'=>true, 'cols'=>2]) !!}
                            {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'selected'=>4, 'label'=>true, 'cols'=>2 ]) !!}
                        </div>
                    </div>
                    <div class="row">
                        {!! buttonAction('', 'بحث <i class="fab fa-searchengin"></i>', 'searchbtn', false) !!}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">التقرير</h6>
                    <div class="row">
                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                    </div>
                    {{-- <div class="toggleColumn">
                        <p style="font-size:0.6em; font-weight: bold; text-decoration: underline; display: inline;">اظهار/إخفاء الأعمدة::</p>
                         <a href="#" class="toggle-vis" data-column="0">تاريخ النظام</a> - 
                         <a href="#" class="toggle-vis" data-column="1">تاريخ البيع</a> - 
                         <a href="#" class="toggle-vis" data-column="2">رقم الفاتورة</a> - 
                         <a href="#" class="toggle-vis" data-column="3">العميل</a> - 
                         <a href="#" class="toggle-vis" data-column="4">المندوب</a> - 
                         <a href="#" class="toggle-vis" data-column="5">النوع</a> - 
                         <a href="#" class="toggle-vis" data-column="6">المحافظة</a> - 
                         <a href="#" class="toggle-vis" data-column="7">المدينة</a> - 
                         <a href="#" class="toggle-vis" data-column="8">المنطقة</a> - 
                         <a href="#" class="toggle-vis" data-column="9">الصنف</a> - 
                         <a href="#" class="toggle-vis" data-column="10">رقم التشغيلة</a> - 
                         <a href="#" class="toggle-vis" data-column="11">الكمية</a> - 
                         <a href="#" class="toggle-vis" data-column="12">بونس</a> - 
                         <a href="#" class="toggle-vis" data-column="13">مرتجع</a> - 
                         <a href="#" class="toggle-vis" data-column="14">مرتجع بونس</a> - 
                         <a href="#" class="toggle-vis" data-column="15">سعر الجمهور</a> - 
                         <a href="#" class="toggle-vis" data-column="16">خصم البيع</a> - 
                         <a href="#" class="toggle-vis" data-column="17">صافي</a> - 
                         <a href="#" class="toggle-vis" data-column="18">المدفوع</a> - 
                         <a href="#" class="toggle-vis" data-column="19">الباقي</a> - 
                         
                         <a href="#" class="toggle-vis" data-column="20">ت أول تحصيل</a> - 
                         <a href="#" class="toggle-vis" data-column="21">ك أول تحصيل</a> - 
                         <a href="#" class="toggle-vis" data-column="22">ت أخر تحصيل</a> - 
                         <a href="#" class="toggle-vis" data-column="23">ك أخر تحصيل</a> - 

                         <a href="#" class="toggle-vis" data-column="24">الإجراءات</a> - 
                    </div> --}}
                    <p>ملحوظة: مجموع عمود الباقي لا يعبر عن مديونية العميل، ولكن يجب أخذ مبلغ محفظة العميل في الإعتبار</p>
                    <p>قيمة الباقي في هذا الجدول تعبر عن قيمة العلب الأجل  فقط</p>
                    <p>لحساب مديونية العميل يجب طرح محفظة العميل من قيمة الباقي، وهذا الطرح موجود جاهز في
                        <a href="{{ route('reports.clientbalancesum') }}"> كشف أرصدة العملاء</a>
                    </p>
                    <p>عمود الكمية يعبر عن كمية الفاتورة وليس الأجل</p>
                    <div class="table-responsive m-t-40">
                        <table class="table table-hover table-bordered mobileTable table-striped" id="yajraTable">
                            <thead>
                                <tr>
                                    <th>تاريخ النظام</th>
                                    <th>تاريخ البيع</th>
                                    <th>رقم الفاتورة</th>
                                    <th>العميل</th>
                                    <th>المندوب</th>
                                    <th>النوع</th>
                                    <th>المحافظة</th>
                                    <th>المدينة</th>
                                    <th>المنطقة</th>
                                    <th>الصنف</th>
                                    <th>رقم التشغيلة</th>
                                    <th>الكمية</th>
                                    <th>بونس</th>
                                    <th>مرتجع</th>
                                    <th>مرتجع بونس</th>
                                    <th>سعر الجمهور</th>
                                    <th>خصم البيع</th>
                                    <th>صافي</th>
                                    <th>المدفوع</th>
                                    <th>الباقي</th>
                                    <th>ت أول تحصيل</th>
                                    <th>ك أول تحصيل</th>
                                    <th>ت أخر تحصيل</th>
                                    <th>ك أخر تحصيل</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>تاريخ النظام</th>
                                    <th>تاريخ البيع</th>
                                    <th>رقم الفاتورة</th>
                                    <th>العميل</th>
                                    <th>المندوب</th>
                                    <th>النوع</th>
                                    <th>المحافظة</th>
                                    <th>المدينة</th>
                                    <th>المنطقة</th>
                                    <th>الصنف</th>
                                    <th>رقم التشغيلة</th>
                                    <th style="font-size: 0.9em; font-weight: bold; background-color: red; color:yellow; ">
                                        <span id="totalQ"></span>
                                        <span style="font-size: .5em; color: white; display: block">كمية</span>
                                    </th>
                                    <th style="font-size: 0.9em; font-weight: bold; background-color: red; color:yellow; ">
                                        <span id="totalBounces"></span>
                                        <span style="font-size: .5em; color: white; display: block">بونس</span>
                                    </th>
                                    <th style="font-size: 0.9em; font-weight: bold; background-color: red; color:yellow; ">
                                        <span id="totalReturns"></span>
                                        <span style="font-size: .5em; color: white; display: block">مرتجع</span>
                                    </th>
                                    <th>مرتجع بونس</th>
                                    <th>سعر الجمهور</th>
                                    <th>خصم البيع</th>
                                    <th style="font-size: 0.9em; font-weight: bold; background-color: red; color:yellow; ">
                                        <span id="totalRequireds"></span>
                                        <span style="font-size: .5em; color: white; display: block">قيمة</span>
                                    </th>
                                    <th style="font-size: 0.9em; font-weight: bold; background-color: red; color:yellow; ">
                                        <span id="totalPaids"></span>
                                        <span style="font-size: .5em; color: white; display: block">مدفوع</span>
                                    </th>
                                    <th style="font-size: 0.9em; font-weight: bold; background-color: red; color:yellow; ">
                                        <span id="totalNexts"></span>
                                        <span style="font-size: .5em; color: white; display: block">باقي</span>
                                    </th>
                                    <th>ت أول تحصيل</th>
                                    <th>ك أول تحصيل</th>
                                    <th>ت أخر تحصيل</th>
                                    <th>ك أخر تحصيل</th>
                                    <th>الإجراءات</th>
                                </tr>
                                
                                
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

<script>
    $('document').ready(function(){
        // Set visiable datatable column in local storage
        let dt_columns = [0,2,4,5,6,7,8,10,20,21,22,23]
        
        if (localStorage.getItem("dt_columns") === null) {
            localStorage.setItem("dt_columns", JSON.stringify(dt_columns));
        }else{
            dt_columns = JSON.parse(localStorage.getItem("dt_columns"));
        }

        
        $(document).on('click', '.buttons-columnVisibility', function(e){
            dt_id = parseInt($(this).attr('data-cv-idx'))
                             //new id 
            console.log(dt_id)
            if(!dt_columns.includes(dt_id)){          //checking weather array contain the id
                dt_columns.push(dt_id);               //adding to array because value doesnt exists
            }else{
                dt_columns.splice(dt_columns.indexOf(dt_id), 1);  //deleting
            }
            localStorage.setItem("dt_columns", JSON.stringify(dt_columns));
        })

        //Start yajra DataTable
        function yajraintialize(form = false){
            $(function() {
                var ajaxDec = {
                    "url": '{!! route('reports.yajrainvoiceget') !!}',
                    "type": "GET",
                    "data": function(d) {
                    var frm_data = $('#searchForm').serializeArray();
                        $.each(frm_data, function(key, val) {
                            d[val.name] = val.value;
                        });
                    }
                }
                var table = $('#yajraTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive" : true,
                    "searching": false,

                    "ajax": ajaxDec,

                    "dataSrc": function (json) {
                        json = JSON.parse(json);
                        return json.data;
                    },

                    drawCallback:function(settings, row, data, start, end, display){
                        $('#totalQ').html(parseInt(settings.json.totalQ));
                        $('#totalBounces').html(parseInt(settings.json.totalBounces));
                        $('#totalRequireds').html(parseFloat(settings.json.totalRequireds).toFixed(2));
                        $('#totalPaids').html(parseFloat(settings.json.totalPaids).toFixed(2));
                        $('#totalNexts').html(parseFloat(settings.json.totalNexts).toFixed(2));
                        $('#totalReturns').html(parseFloat(settings.json.totalReturns));
                    },

                    // "footerCallback": function ( row, data, start, end, display ) {
                    //     var api = this.api(), data;

                    //     // converting to interger to find total
                    //     var intVal = function ( i ) {
                    //         return typeof i === 'string' ?
                    //             i.replace(/[\$,]/g, '')*1 :
                    //             typeof i === 'number' ?
                    //                 i : 0;
                    //     };

                    //     // computing column Total the complete result
                    //     var monTotal = api
                    //         .column( 10)
                    //         .data()
                    //         .reduce( function (a, b) {
                    //             return intVal(a) + intVal(b);
                    //         }, 0 );


                    //     $("#totalQ").html(monTotal)
                    // },

                    "columns":[
                        { data: 'created_at', name: 'created_at'},
                        { data: 'invoice_date', name: 'invoice_date'},
                        { data: 'invoice_code', name: 'invoice_code'},
                        { data: 'client_region_title', name: 'client_region_title'},
                        { data: 'user_rep_fullName', name: 'user_rep_fullName'},
                        { data: 'client_type_name', name: 'client_type_name'},
                        { data: 'state', name: 'state'},
                        { data: 'city', name: 'city'},
                        { data: 'r_name', name: 'r_name'},
                        { data: 'Product_Name', name: 'Product_Name'},
                        { data: 'runID', name: 'runID'},
                        { data: 'invoice_net_q_withoutbounce', name: 'invoice_net_q_withoutbounce'},
                        { data: 'invoice_bounce_net', name: 'invoice_bounce_net'},
                        { data: 'return_quantity', name: 'return_quantity'},
                        { data: 'return_bounce', name: 'return_bounce'},
                        { data: 'invoice_public_price', name: 'invoice_public_price'},
                        { data: 'discount', name: 'discount'},
                        { data: 'get_required', name: 'get_required'},
                        { data: 'get_paid', name: 'get_paid'},
                        { data: 'get_next', name: 'get_next'},
                        { data: 'first_get_date', name: 'first_get_date'},
                        { data: 'first_get_quantity', name: 'first_get_quantity'},
                        { data: 'last_get_date', name: 'last_get_date'},
                        { data: 'last_get_quantity', name: 'last_get_quantity'},


                        { data: 'actions', name: 'actions'},
                    ],


                    "columnDefs": [
                        // { "orderable": false, "twargets": 0},

                        { "visible": false, "targets": dt_columns },

                        { "width": ".02%",  "targets": [9] },       //Show on all devices
                        // { "width": ".02%",  "targets": [19] },       //Show on all devices
                        // { "className": 'cellcode', "targets": [0,1,2] },       //Show on all devices
                        { "className": 'not-mobile', "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24] },       //Show on all devices
                        { "className": 'mobile', "targets": [9,19] },       //Show on all devices
                    ],



                    "language": {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Arabic.json'
                    },
                    "dom": 'Blfrtip',
                    // "buttons": ['copy', 'excel','print'],

                    "buttons": [
                        {
                            extend: 'copyHtml5',
                            footer: true,
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            footer: true,
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            footer: true,
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        'colvis'
                    ],


                    "displayLength": 25,
                    "pageLength": 25,
                    "lengthMenu": [[10, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [10, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]

                });

                // $(document).on('click', 'a.toggle-vis', function(e){
                //     // alert("ok")
                //     e.preventDefault();
            
                //     // Get the column API object
                //     var column = table.column( $(this).attr('data-column') );
                //     if(!column.visible()){
                //         $(this).css({'color': '#00f'})
                //     }else{
                //         $(this).css({'color': '#666'})
                //     }
                    
                //     // Toggle the visibility
                //     column.visible( ! column.visible() );
                // } );
            });
        }
        yajraintialize(false)
       


        //End yajra DataTable
        $(document).on('submit', '#searchForm', function(e){
            $(this).closest('#yajraTable input').remove();
            $("#yajraTable").dataTable().fnDestroy()
            yajraintialize(true)
            e.preventDefault();
            return false;
        })
        $('select').selectpicker();

         //Region JSCode
         function state_id(){
            if($('#state_id').val()){
                $.ajax({
                    url: '{{ route('regions.cities') }}',
                    data:{state_id:$('#state_id').val() @if(old('city_id')), edit_id:{{ old('city_id') }}@endif},
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token()}}'
                    },
                    cache: false,

                    success: function (data) {
                        $('#city_idrow>.city_id').remove()
                        data = data.replace("col-md-12", "col-md-2 col-sm-12");
                        $('div.region_id').remove()
                        $('#city_idrow').append(data)


                    },
                    error: function (xhr) {
                        alert("Error: - " + xhr.status + " " + xhr.statusText);
                    }
                });
            }

        }

        state_id()
        $('#state_id').change(function(){
            $("#region_id").remove();
            state_id()
        })

        after = '<div id="region_idrow" class="region_idrow col-md-6 col-sm-12 region_idrow"></div>';
        function city_id(){
            if($('#city_id').val()){
                $.ajax({
                    url: '{{ route('cities.regions') }}',
                    data:{city_id:$('#city_id').val() @if(old('region_id')), edit_id:{{ old('region_id') }}@endif},
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token()}}'
                    },
                    cache: false,

                    success: function (data) {
                        $('#region_idrow>.region_id').remove()
                        data = data.replace("col-md-12", "col-md-2 col-sm-12");
                        $('div.region_id').remove()
                        $('#city_idrow .region_idrow').remove()
                        $('#city_idrow').append(after)
                        $('#city_idrow #region_idrow').replaceWith(data)
                    },
                    error: function (xhr) {
                        alert("Error: - " + xhr.status + " " + xhr.statusText);
                    }
                });
            }
        }

        city_id()
        $(document).on("change", "#city_id", function(e){
            city_id()

        })
        //End Region JSCode


    });
</script>
<style>
    #searchbtn{
        float: left;
    }
</style>
@endsection

