@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'تقرير التحصيل'])

@section('content')
<?php $start_time = microtime(true); ?>
<style>
    .toggleColumn a{
        color: blue;
        font-size: 0.7em;
    }
</style>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تقرير التحصيل</h4>
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
                    <h6 class="card-title">تقرير التحصيل</h6>
                    <form id="searchForm" class="searchForm">
                        <div id="city_idrow" class="row city_idrow">
                            {!! select(['errors'=>$errors, 'name'=>'client_id', 'frkName'=>'client_name_title', 'rows'=>$clients, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}
                            {!! select(['errors'=>'', 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}
                            {!! input(['errors'=>$errors, 'name'=>'get_date_from', 'type'=>'date', 'value'=>Carbon\Carbon::now()->startOfMonth()->isoFormat('YYYY-MM-DD'), 'transval'=>'من (تاريخ التحصيل)', 'label'=>true, 'cols'=>2]) !!}
                            {!! input(['errors'=>$errors, 'name'=>'get_date_to', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'إالي (تاريخ التحصيل)', 'label'=>true, 'cols'=>2]) !!}
                            {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'selected'=>4, 'label'=>true, 'cols'=>2 ]) !!}
                        </div>
                        <p style="display: none" id="sql">Query</p>
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
                    <h6 class="card-title">تقرير التحصيل</h6>
                    <div class="row">
                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                    </div>
                    
                    <div class="table-responsive m-t-40">
                        <table class="table table-hover table-bordered mobileTable table-striped" id="yajraTable">
                            <thead>
                                <tr>
                                    <th>تاريخ التحصيل</th>
                                    <th>العميل</th>
                                    <th>المحافظة</th>
                                    <th>المدينة</th>
                                    <th>المنطقة</th>
                                    <th>الصنف</th>
                                    <th>الكمية</th>
                                    <th>سعر الجمهور</th>
                                    <th>خصم البيع</th>
                                    <th>قيمة السداد</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>تاريخ التحصيل</th>
                                    <th>العميل</th>
                                    <th>المحافظة</th>
                                    <th>المدينة</th>
                                    <th>المنطقة</th>
                                    <th>الصنف</th>
                                    <th style="font-size: 0.9em; font-weight: bold; background-color: red; color:yellow; ">
                                        <span style="font-size: .8em; color: white; display: block">كمية</span>
                                        <span id="totalQ"></span>
                                    </th>
                                    <th>سعر الجمهور</th>
                                    <th>خصم البيع</th>
                                   
                                    <th style="font-size: 0.9em; font-weight: bold; background-color: red; color:yellow; ">
                                        <span style="font-size: .7em; color: white; display: block">سداد</span>
                                        <span id="totalPaids"></span>
                                        <span style="font-size: .7em; color: white; display: block">محفظة</span>
                                        <span id="wallet_sum"></span>
                                        <hr style="color: white; background: white; height: 3px">
                                        <span style="font-size: .8em; color: black; display: block">صافي تحصيل</span>
                                        <span style="color: white" id="client_pay"></span>
                                    </th>
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
        //Start yajra DataTable
        function changePageTitle(){
            title = ''
            state = $('#state_id').find("option:selected").text();
            if(state != 'اختر المحافظة'){
                title += state + ' - '
            }
            title += 'تقرير التحصيل في الفترة من '
            get_date_from = $('#get_date_from').val()
            get_date_to = $('#get_date_to').val()
            title += get_date_from + ' الي  '+ get_date_to
            if(state != 'اختر المحافظة'){
                title += ' لمحافظة ' + state
            }
            document.title = title;
            $('.card-title').html(title)
        }
        changePageTitle()
        $(document).on("change", "#product_id, #get_date_from, #get_date_to, #state_id", function () {
            changePageTitle()
        })
        
        function yajraintialize(form = false){
            $(function() {
                var ajaxDec = {
                    "url": '{!! route('reports.yajragetsreport') !!}',
                    "type": "GET",
                    "data": function(d) {
                    var frm_data = $('#searchForm').serializeArray();
                        $.each(frm_data, function(key, val) {
                            d[val.name] = val.value;
                        });
                    }
                }
                var table = $('#yajraTable').DataTable({
                    "customize": function ( doc ) {
                        $(doc.document.body).find('h1').css('font-size', '10pt');
                        $(doc.document.body).find('h1').css('text-align', 'center'); 
                    },

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
                        $('#totalPaids').html(parseFloat(settings.json.totalPaids).toFixed(2));
                        $('#wallet_sum').html(parseFloat(settings.json.wallet_sum).toFixed(2));
                        $('#client_pay').html(parseFloat(settings.json.client_pay).toFixed(2));
                        // $('#sql').html(settings.json.sql);
                    },

                   

                    "columns":[
                        { data: 'get_date', name: 'get_date'},
                        { data: 'client_name', name: 'client_name'},
                        { data: 'state', name: 'state'},
                        { data: 'city', name: 'city'},
                        { data: 'vilage', name: 'vilage'},
                        { data: 'Product_Name', name: 'Product_Name'},
                        { data: 'get_quantity', name: 'get_quantity'},
                        { data: 'invoice_public_price', name: 'invoice_public_price'},
                        { data: 'discount', name: 'discount'},
                        { data: 'client_pay_for_q', name: 'client_pay_for_q'},
                        
                    ],


                    "columnDefs": [
                        // { "orderable": false, "twargets": 0},
                        { "width": "1%",  "targets": [4] },       //Show on all devices
                        // { "className": 'cellcode', "targets": [0,1,2] },       //Show on all devices
                        { "className": 'not-mobile', "targets": [0,1,2,3,4,5,6,7] },       //Show on all devices
                        { "className": 'mobile', "targets": [0,1,2,3,4] },       //Show on all devices
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


                    "displayLength": 50,
                    "pageLength": 50,
                    "lengthMenu": [[20, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [20, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]

                });

                $(document).on('click', 'a.toggle-vis', function(e){
                    // alert("ok")
                    e.preventDefault();
            
                    // Get the column API object
                    var column = table.column( $(this).attr('data-column') );
                    if(!column.visible()){
                        $(this).css({'color': '#00f'})
                    }else{
                        $(this).css({'color': '#666'})
                    }
                    
                    // Toggle the visibility
                    column.visible( ! column.visible() );
                } );
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

