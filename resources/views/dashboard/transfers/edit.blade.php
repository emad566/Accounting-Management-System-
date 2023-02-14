@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل أمر التحويل : {{ $transfer->transfer_code }}</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('transfers.index') }}" class="btn btn-primary float-right">كل أومر التحويلات</a>

                @if (Auth::user()->can(['CRUD Transfers']) && $transfer->transfer_status_id ==10)
                <a href="{{ route('transfers.destroy', $transfer->id) }}" class="btn btn-danger float-right mx-2">حذف</a>
                <a href="{{ route('transfers.show', $transfer->id) }}" class="btn btn-primary float-right mx-2">عرض</a>
                @endif
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
                    <h6 class="card-title">تفاصيل أمر التحويل: <a href="{{ route('transfers.show', $transfer->id) }}" class="btn btn-primary">{{ $transfer->transfer_code }}</a></h6>
                <hr>
                    @php $cols = 3; @endphp
                    @include('dashboard.transfers.editModel')
                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection
@section('script')
<script>
    $('document').ready(function(){
        lastCount = parseInt({{ $transfer->products->count() }}) +1;

        function cancelEDit() {
            $("#inpermitAdd").html('<i class="fa fa-plus-circle"></i>أضف')
            $("#inpermitAdd").attr('editAdd', 'add')
            $(".product_id").val('')
            $(".runID").val('')
            $(".avliable").val('')
            $("#cancelEdit").hide()
            $(".inptutNull").val('')
        }

        function changeProductId(selectRunID="") {
            if(!$("#product_id").val()){
                alert("من فضلك اختر منتج!")
                $(this).val('')
                $("#product_id").focus()

            }else{
                store_id =$("#from_store_id").val()
                product_id = $("#product_id").val()

                if(product_id && store_id){

                    $.ajax({
                        url: "{{ url("dashboard/transfer/") }}/" +store_id+"/"+product_id+"/getrunids/"+{{ $transfer->id }},
                        // data:{
                        //     _token: '{!! csrf_token() !!}',
                        // },
                        type: 'GET',
                        cache:false,
                        success: function(data){
                            if(data == false){
                            }else{
                                $("div .runID").replaceWith(data)

                                if(selectRunID){
                                    changeRunID(selectRunID)
                                    $("#runID").val(selectRunID)
                                }else if($('#runID').val()){
                                    runID=$("#runID").val()
                                    store_id =$("#from_store_id").val()
                                    product_id = $("#product_id").val()
                                    if(product_id && runID && store_id){
                                        changeRunID(runID)
                                    }
                                }else{
                                    $('#avliable').val('')
                                }
                                if(!selectRunID){
                                    $('#Quantity').val('')
                                    $('#rest').val('')
                                }
                            }
                        },
                        error: function(xhr){
                            alert(xhr.status+' '+xhr.statusText);
                        }
                    });
                }
            }
        }

        function changeRunID(selectRunID="") {
            if(!$("#product_id").val()){
                alert("من فضلك اختر منتج!")
                $('#avliable').val('')
                $("#runID").val('')
                $('#Quantity').val('')
                $('#rest').val('')
                $("#product_id").focus()

            }else{
                store_id =$("#from_store_id").val()
                runID=(selectRunID)? selectRunID : $("#runID").val();
                product_id = $("#product_id").val()


                if(product_id && runID && store_id){
                    $.ajax({
                        url: "{{ url("dashboard/transfer/") }}/" +product_id+"/"+runID+"/"+store_id+"/"+{{ $transfer->id }},
                        // data:{
                        //     _token: '{!! csrf_token() !!}',
                        // },
                        type: 'GET',
                        cache:false,
                        success: function(data){
                                if(data == false){
                                    $('#avliable').val('')
                                    $('#Quantity').attr('max', 0)
                                }else{
                                    $('#avliable').val(data)
                                    $('#Quantity').attr('max', data)
                                }

                                if(!selectRunID){
                                    $('#Quantity').val('')
                                    $('#rest').val('')
                                }else{
                                    rest = parseInt(data) - parseInt($('#Quantity').val())
                                    $('#rest').val(rest)
                                }
                            },
                        error: function(xhr){
                            alert(xhr.status+' changeRunID '+xhr.statusText);
                        }
                    });
                }
            }
        }

        $(document).on("click", ".productEdit", function(e){
            product_id = $(this).attr('product_id')
            runID = $(this).attr('runID')
            quantity = $(this).attr('quantity')
            id_runID = product_id+ "_" + runID

            $("#product_id").val(product_id)
            $("#runID").val(runID)
            $("#Quantity").val(quantity)



            changeProductId(runID)

            $("#runID").val(runID)
            $("#quantity").val(quantity)

            $("#inpermitAdd").html('<i class="fa fa-save"></i>حفظ')
            $("#inpermitAdd").attr('editAdd', 'edit')
            $("#inpermitAdd").attr('delId', 'row'+id_runID)
            $("#cancelEdit").show()
            e.preventDefault()
        })


        $(document).on("click", "#cancelEdit", function(e){
            cancelEDit()
        })

        $(document).on("click", "#inpermitAdd", function(e){

            if(!$("#runID").val() || !$("#product_id").val() || !$("#Quantity").val() || $("#Quantity").val() <1){
                e.preventDefault();
                alert("من فضلك اختار صنف وكمية موجبة أقل من 9999999..! وباقي بيانات الصنف بشكل صحيح.")
                return ;
            }

            id_runID = $("#product_id").val()+ "_" + $("#runID").val()
            editAdd = $(this).attr('editAdd')


            if($("#row"+ id_runID).length && editAdd == 'add'){
                e.preventDefault();
                alert(" هذا الصنف موجود بالفعل في اذن التحويل يمكنك تغيير كميته بدل من إضافته مرة أخري.!")
                return ;
            }

            $newRow = '<tr id="row'+  id_runID +'">'
                        + '<td>'+ lastCount + '</td>'
                        + '<td data-th="الصنف"> <input type="hidden" name="product_ids[]" value="'+ $("#product_id").val() +'">' + $("#product_id option:selected").text() + '</td>'
                        + '<td data-th="{{ trans('validation.attributes.runID') }}"> <input type="text" name="runIDs[]" value="'+ $("#runID").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td data-th="الكمية"> <input type="number" name="quantities[]" value="'+ $("#Quantity").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td> <a href="#" delId="row'+  id_runID +'" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a>'
                            + '<a href="#"'
                            + 'product_id="'+ $("#product_id").val() +'"'
                            + 'runID="'+ $("#runID").val() +'"'
                            + 'Quantity="'+ $("#Quantity").val() +'"'
                            + 'delId="row'+  id_runID +'" class="productEdit"><i class="fas fa-edit delEdit"></i></a>'
                        + '</td>'
                     +'<tr>';

            lastCount++;

            $("#Quantity").val('')

            $("#product_id").val('')
            $("#runID").val('')
            $("#rest").val('')
            $("#avliable").val('')

            if(editAdd == 'edit'){
                id_runIDdel = $(this).attr('delId')
                $("#"+ id_runIDdel).replaceWith($newRow)
            }else if(editAdd == 'add'){
                tableBody = $("#productsTable tbody");
                tableBody.append($newRow);
            }

            $("#product_id").focus()
            cancelEDit()
        });

        $(document).on("click", ".prodcutDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            e.preventDefault();
        });

        $(document).on("change", "#product_id", function(e){
            changeProductId()
        });

        $(document).on("change", "#runID", function(e){
            changeRunID()
        });

        $(document).on("change", "#Quantity", function(e){
            if(!$("#product_id").val()){
                alert("من فضلك اختر منتج!")
            }else{
                rest = $('#avliable').val() - $(this).val();
                if(rest<0 || rest >= $('#avliable').val()){
                    alert (" الكمية يجب ان تكون اقل من المتاح و أكبر من الصفر!")
                    $(this).val('')
                    $('#rest').val('')
                    $(this).focus()
                }else
                $('#rest').val(rest)
            }

        })

        $(document).on("change", "#from_store_id", function(e){
            from_store_id_chaneg()
        })

        function from_store_id_chaneg(){
            from_store_id = $("#from_store_id").val()
            if(from_store_id){
                $.ajax({
                    url: "{{ url("dashboard/transfer/fromto") }}/" + from_store_id + "/" + {{ $transfer->id }},
                    // data:{
                    //     _token: '{!! csrf_token() !!}',
                    // },
                    type: 'GET',
                    cache:false,
                    success: function(data){
                            if(data == false){

                            }else{
                                $(".product_id").replaceWith(data)
                            }

                        },
                    error: function(xhr){
                            alert(xhr.status+' '+xhr.statusText);
                        }
                });
            }
        }

        from_store_id_chaneg()

    })
</script>
@endsection


