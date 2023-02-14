@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'العملاء'])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">كل العملاء</h4>
        </div>
        <div class="col-md-7 align-self-center text-right" dir="rtl">
            <div class="d-flex justify-content-end align-items-center">

                <div id="verticalcenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="vcenter">أضف عميل</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body text-left">
                                @php $cols = 12; @endphp
                                @include('dashboard.clients.createModel')
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">{{ trans('main.Close') }}</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <a href="{{ route('clients.create') }}" data-toggle="modal" data-target="#verticalcenter" class="btn btn-info  m-l-15"><i class="fa fa-plus-circle"></i> {{ trans('main.Add New') }}</a>
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
                    <h4 class="card-title">كل العملاء</h4>
                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}

                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('clients.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <div class="table-responsive m-t-40">
                            <table class="table table-bordered mobileTable" id="clientsTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" name="allItems" value="1" id="allItems" class="allItems"></th>
                                        <th>اسم العميل</th>
                                        <th>المنطقة</th>
                                        <th>المدينة</th>
                                        <th>المحافظة</th>
                                        <th>الهاتف</th>
                                        <th>مفعل</th>
                                        <th>الأجراءات</th>
                                    </tr>
                                </thead>
                            </table>
                            @if(Auth::user()->can(['Delete_Client'])){
                            <a href="" formid="delete-formMulti" class="deleteMe btn btn-outline-danger btn-min-width box-shadow-3 mr-1 mb-1">حذف</a>
                            @endif
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
    $(document).ready(function(){
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
                        $('#region_idrow').append(data)
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


        $(function() {

            $(function() {
                $('#clientsTable').addClass('datatable');

                var table = $('#clientsTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "ajax": '{!! route('clients.yajraclients') !!}',
                    "columns":[
                        { data: 'DT_RowData.data-id', name: 'id' },
                        { data: 'client_name', name: 'client_name' },
                        { data: 'r_name', name: 'r_name' },
                        { data: 'city', name: 'city' },
                        { data: 'state', name: 'state' },
                        { data: 'client_phone', name: 'client_phone' },
                        { data: 'DT_RowData.data-is_active', name: 'is_active' },
                        { data: 'actions', name: 'actions' },
                    ],

                    'createdRow': function( row, data, dataIndex ) {
                        $( row ).find('td:eq(0)').attr('data-th', '');
                        $( row ).find('td:eq(1)').attr('data-th', 'اسم العميل');
                        $( row ).find('td:eq(2)').attr('data-th', 'المنطقة');
                        $( row ).find('td:eq(3)').attr('data-th', 'المدينة');
                        $( row ).find('td:eq(4)').attr('data-th', 'المحافظة');
                        $( row ).find('td:eq(5)').attr('data-th', 'الهاتف');
                        $( row ).find('td:eq(6)').attr('data-th', 'مفعل');
                        $( row ).find('td:eq(7)').attr('data-th', 'الأجراءات');
                    },

                    "columnDefs": [
                        { "orderable": false, "targets": 0},
                        { "className": 'device', "targets": 0 },       //Show on all devices
                        { "className": 'cellcode', "targets": [1,7] },       //Show on all devices
                        { "className": 'all', "targets": [1,7] },
                    ],

                    "language": {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Arabic.json'
                    },
                    "dom": 'Blfrtip',
                    "buttons": ['copy', 'excel','print'],

                    "displayLength": 50,
                    "pageLength": 50,
                    "lengthMenu": [[10, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [10, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]

                });

                // Order by the grouping
                $('.datatable tbody').on('click', 'tr.group', function() {
                    var currentOrder = table.order()[0];
                    if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                        table.order([2, 'desc']).draw();
                    } else {
                        table.order([2, 'asc']).draw();
                    }
                });
            });

            
        });

        
    });

    function copyid(){
        document.querySelectorAll('.pclientname').forEach(elm => {
            elm.onclick=function (){
                navigator.clipboard.writeText(this.dataset.clientid);
            }
        });
    }

    setTimeout(() =>     {
        copyid();
        document.querySelector('#clientsTable_filter input').onfocusout = function(){
            copyid();
        }
    }, 3000);
</script>
@endsection



