@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">إضافة مخزن</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('stores.index') }}" class="btn btn-primary float-right">كل المخازن</a>
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
                    <h6>إضافة مخزن</h6>
                    <hr>
                    @php $cols = 3; @endphp
                    @include('dashboard.stores.createModel')
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


        $(document).on("click", "#addElement", function(e){
            lastCount = parseInt($("#lastCount").val()) +1;
            get_state_id = $("#state_id").val()
            if(get_state_id){
                get_city_id = $("#city_id").val()
                get_region_id = $("#region_id").val()

                state_txt = $("#state_id option:selected").text()
                city_txt = (get_city_id)? $("#city_id option:selected").text() : '';
                region_txt = (get_region_id)? $("#region_id option:selected").text() : '';

                if(!get_region_id && !get_city_id){
                    get_region_id = get_state_id
                }else if(!get_region_id && get_city_id){
                    get_region_id = get_city_id
                }

                if($("#rowRegion"+ get_region_id).length){
                    e.preventDefault();
                    return ;
                }
                $newRow = '<tr id="rowRegion'+ get_region_id +'">'
                            +    '<td>' + lastCount
                            +        '<input type="hidden" name="regions[]" value="'+ get_region_id +'" id="region_'+ get_region_id +'" class="region_'+ get_region_id +' regions">'
                            +    '</td>'
                            +    '<td>'+ state_txt +'</td>'
                            +    '<td>'+ city_txt +'</td>'
                            +    '<td>'+ region_txt +'</td>'
                            + '<td> <a href="#" delId="rowRegion'+  get_region_id +'" class="elementDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>'
                            +'<tr>'

                lastCount++;
                $("#lastCount").val(lastCount)
                tableBody = $("#productsTable tbody");
                tableBody.append($newRow);

                $("#state_id").val($("#state_id option:first").val());
                $("#city_id").remove()
                $("#region_id").remove()
            }else{
                alert("من فضلك إختر محافظة اولا، حتي تتمكن من إضافة منطقة")
            }
            $("#state_id").focus()
        });

        $(document).on("click", ".elementDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            e.preventDefault();
        });

    });
</script>
@endsection

