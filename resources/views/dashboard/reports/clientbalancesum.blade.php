@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'كشف أرصدة العملاء'])

@section('content')
<?php $start_time = microtime(true); ?>
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
                    <h6 class="card-title">أرصدة العملاء</h6>
                    <form id="searchForm" class="searchForm">
                        <div class="row">
                            {!! select(['errors' => $errors, 'name' => 'client_id', 'frkName' => 'client_name_title', 'rows' => $clients, 'transval' => 'اختر العميل', 'label' => true, 'cols' => 3, 'attr' => 'data-live-search="true"']) !!}
                            {!! select(['errors'=>'', 'name'=>'client_type_id', 'frkName'=>'name', 'rows'=>$client_types, 'transAttr'=>true, 'label'=>true, 'cols'=>3, 'attr'=>'data-live-search="true"']) !!}
                        </div>
                        <div id="city_idrow" class="row city_idrow">
                            @if(Auth::user()->can(['show_reports']))
                            {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'selected'=>4, 'label'=>true, 'cols'=>4 ]) !!}
                            @endif
                        </div>
                        <div class="row">
                            {!! buttonAction('', 'بحث <i class="fab fa-searchengin"></i>', 'searchbtn', false) !!}
                        </div>
                    </form>
                    <p>عمود الرصيد = قيمة كميات   الأجل - مبلغ المحفظة</p>
                </div>
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

                    <div class="table-responsive m-t-40">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 "><p class=""><span id="Label" class="printDataLable">رصيد المحفظة</span>:  <span id="clientOverPrice" class="printDataVal "></span></p></div>
                        </div>
                        <table class="table table-hover table-bordered mobileTable table-striped" id="yajraTable">
                            <thead>
                                <tr>
                                    <th>العميل</th>
                                    <th>المحفظة</th>
                                    @if(Auth::user()->can(['show_reports']))
                                    <th>مدين</th>
                                    <th>دائن</th>
                                    @endif

                                    @if(Auth::user()->can(['show_reports']))
                                    <th>رصيد</th>
                                    @elseif(Auth::user()->can(['Reports_Accounting']))
                                    <th>باقي</th>
                                    @endif

                                    @if(Auth::user()->can(['show_reports']))
                                    <th>المحافظة</th>
                                    <th>المدينة</th>
                                    <th>المنطقة</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1">إجمالي</td>
                                    <td id="get_overPrice_sum"></td>
                                    @if(Auth::user()->can(['show_reports']))
                                    <td id="get_requireds"></td>
                                    <td id="client_pays"></td>
                                    @endif
                                    <td id="get_client_nexts"></td>
                                    @if(Auth::user()->can(['show_reports']))
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @endif
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
        function yajraintialize(form = false){
            $(function() {
                var ajaxDec = {
                    "url": '{!! route('reports.yajraclientbalancesum') !!}',
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
                        $('#get_overPrice_sum').html(parseFloat(settings.json.get_overPrice_sum).toFixed(2))
                        $('#get_requireds').html(parseFloat(settings.json.get_requireds).toFixed(2))
                        $('#client_pays').html(parseFloat(settings.json.client_pays).toFixed(2))
                        $('#get_client_nexts').html(parseFloat(settings.json.get_client_nexts).toFixed(2))
                    },


                    "columns":[
                        { data: 'client_name', name: 'client_name'},
                        { data: 'get_overPrice_sum', name: 'get_overPrice_sum'},
                        @if(Auth::user()->can(['show_reports']))
                        { data: 'get_requireds', name: 'get_requireds'},
                        { data: 'client_pays', name: 'client_pays'},
                        @endif
                        { data: 'get_client_nexts', name: 'get_client_nexts'},
                        @if(Auth::user()->can(['show_reports']))
                        { data: 'state', name: 'state'},
                        { data: 'city', name: 'city'},
                        { data: 'r_name', name: 'r_name'}
                        @endif
                    ],


                    // "columnDefs": [
                    //     { "orderable": false, "targets": 0},
                    //     { "width": "1%",  "targets": [3] },       //Show on all devices
                    //     { "className": 'cellcode', "targets": [0,1] },       //Show on all devices
                    //     { "className": 'not-mobile', "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16] },       //Show on all devices
                    //     { "className": 'mobile', "targets": [0,1,2,3] },       //Show on all devices
                    // ],



                    "language": {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Arabic.json'
                    },
                    "dom": 'Blfrtip',
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
                    "lengthMenu": [[50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]

                });
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
                        data = data.replace("col-md-12", "col-md-4 col-sm-12");
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
                        data = data.replace("col-md-12", "col-md-4 col-sm-12");
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

