@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor card-title">إنشاء فاتورة مشتريات</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('inpermits.index') }}" class="btn btn-primary float-right">كل فواتير المشتريات</a>
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
                    <h6>إنشاء فاتورة مشتريات</h6>
                    <hr>
                    @php $cols = 3; @endphp
                    @include('dashboard.inpermits.createModel')
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
        var products = null
        $("#loginform").submit(function(){
            $("#form-actionSubmit").html('<p>جاري الحفظ ... الرجاء الانتظار ...</p>')
        });

        lastCount = parseInt($("#lastCount").val()) +1;

        $(document).on("click", "#inpermitAdd", function(e){
            if(!$("#Buy_Price").val() || !$("#runID").val() || !$("#expire_date").val() || !$("#create_date").val() || !$("#product_id").val() || !$("#Quantity").val() || $("#Quantity").val() <1){
                e.preventDefault();
                alert("من فضلك اختار صنف وكمية موجبة أقل من 9999999..! وباقي بيانات الصنف بششكل صحيح.")
                return ;
            }

            id_runID = $("#product_id").val()+ "_" + $("#runID").val().replace('.', '')
            if($("#row"+id_runID).length){
                e.preventDefault();
                alert(" هذا الصنف موجود بالفعل في اذن الإضافة يمكنك تغيير كميته علي أن تضيفه مرة أخري.!")
                return ;
            }

            if(parseFloat($("#Buy_Price").val()) <0 &&  parseFloat($("#Buy_Price").val()) > 100){
                e.preventDefault();
                alert(" خصم الشراء غير متوافق.!")
                return ;
            }

            Public_Price = parseFloat($("#Public_Price").val())
            quantity = parseInt($("#Quantity").val())
            discount = parseFloat($("#Buy_Price").val())
            price = (100-discount)/100*Public_Price*quantity
            // alert(price)
            price = parseFloat(price).toFixed(2)
            $newRow = '<tr id="row'+ id_runID.replace('.', '') +'">'
                        + '<td data-th="#">'+ lastCount + '</td>'
                        + '<td data-th="الصنف"> <input type="hidden" id="product_id'+$("#product_id").val()+'" name="product_ids[]" value="'+ $("#product_id").val() +'">' + $("#product_id option:selected").text() + '</td>'
                        + '<td data-th="{{ trans("validation.attributes.runID") }}"> <input proId="'+$("#product_id").val()+'" type="text" id="runID'+$("#product_id").val()+'" name="runIDs[]" value="'+ $("#runID").val() +'" class="form-control runIDedit" min="0" max="99999999"  readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.create_date") }}"> <input type="date" id="create_date'+$("#product_id").val()+'" name="create_dates[]" value="'+ $("#create_date").val() +'" class="form-control" readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.expire_date") }}"> <input type="date" id="expire_date'+$("#product_id").val()+'" name="expire_dates[]" value="'+ $("#expire_date").val() +'" class="form-control" readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.Buy_Price") }}"> <input type="number" id="Public_Price'+$("#product_id").val()+'" name="Public_Prices[]" value="'+ $("#Public_Price").val() +'" class="form-control" min="0" max="99999999" step="0.01" readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.Buy_Price") }}"> <input type="number" id="Buy_Price'+$("#product_id").val()+'" name="Buy_Prices[]" value="'+ $("#Buy_Price").val() +'" class="form-control" min="0" max="100" step="0.01" readonly></td>'
                        + '<td data-th="الكمية"> <input type="number" id="quantitie'+$("#product_id").val()+'" name="quantities[]" value="'+ $("#Quantity").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td data-th="القيمة"> <input type="number" id="price'+$("#product_id").val()+'" name="prices[]" value="'+ price +'" class="form-control prices" readonly></td>'
                        + '<td data-th="حذف"> <a href="#" delId="row'+ id_runID.replace('.', '') +'" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>'
                     +'<tr>';

            lastCount++

            $("#Quantity").val('')

            $("#product_id").val('')
            $("#runID").val('')
            $("#create_date").val('')
            $("#expire_date").val('')
            $("#Buy_Price").val('')
            tableBody = $("#productsTable tbody");
            tableBody.append($newRow);

            totalPrice = 0;
            $(".prices").each(function () {
                totalPrice += parseFloat($(this).val())
            })
            $("#totalPrice").val(parseFloat(totalPrice).toFixed(2))

        });

        $(document).on("click", ".prodcutDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            e.preventDefault();
        });

        // $(document).on("change", "#product_id", function(e){
        //     alert(products)
        // })

        $(document).on("change", "#product_id", function(e){
            var Public_Price;
            if($(this).attr('id')=="product_id"){
                Public_Price = $('option:selected', this).attr('Public_Price');
                $(this).attr('Public_Price', Public_Price)
                $("#Public_Price").val(Public_Price)
                $("#runID").val(Public_Price)
                $("#Buy_Price").val(60)
                $("#create_date").val("2020-01-01")
                $("#expire_date").val("2020-01-01")
                $("#Quantity").val(100000)
                $("#inpermitAdd").focus();
            }

            Public_Price = $("#product_id").attr('Public_Price')
            $("#Public_Price").val(Public_Price)
            
            try{
                product_id = $('#product_id').val();
                if(product_id){
                    $.ajax({
                        url: "{{ url("dashboard/inpermitsgetrunid/") }}/" +product_id,
                        type: 'GET',
                        cache:false,
                        success: function(runids){
                                if(runids == false){

                                }else{
                                    list = '<datalist id="runids">'
                                    i = 0
                                    runids.forEach(rid => {
                                        selected = i==0? "selected" : '';
                                        i++
                                        list += '<option value="'+rid.runID+'" '+selected+'> سعر'+rid.Public_Price+ '</option>'
                                    });
                                    list += '<datalist>'
                                    $("#runids").replaceWith(list) 
                                    $("#runID").val('')
                                    $('.isDisabled').removeAttr("disabled")
                                    $('#Public_Price').removeAttr("disabled")
                                    $('.isDisabled').val("")
                                }

                            },
                        error: function(xhr){
                                alert(xhr.status+' '+xhr.statusText);
                            }
                    });
                }

            }catch(e){
                alert(e.message);
            }

        });

        $(document).on("change", "#runID", function(e){
            if($(this).attr('id')=="product_id"){
                Public_Price = $('option:selected', this).attr('Public_Price');
                $(this).attr('Public_Price', Public_Price)
            }
            Public_Price = $("#product_id").attr('Public_Price')
            $("#Public_Price").val(Public_Price)
            try{
                product_id = $('#product_id').val();
                runID =$('#runID').val();
                
                if(product_id && runID){
                    $.ajax({
                        url: "{{ url("dashboard/inpermit/") }}/" +product_id+"/"+runID,
                        // data:{
                        //     _token: '{!! csrf_token() !!}',
                        // },
                        type: 'GET',
                        cache:false,
                        success: function(inpermitPro){
                                if(inpermitPro == false){
                                    $('.isDisabled').removeAttr("disabled")
                                    $('#Public_Price').removeAttr("disabled")
                                    $('.isDisabled').val("")

                                }else{
                                    $('.isDisabled').attr('disabled', 'disabled')
                                    $('#Public_Price').attr('disabled', 'disabled')
                                    Public_Price = (inpermitPro.Public_Price)? inpermitPro.Public_Price :  $("#product_id").attr('Public_Price');
                                    $("#Public_Price").val(Public_Price)
                                    $("#Buy_Price").val(inpermitPro.Buy_Price)
                                    $("#create_date").val(inpermitPro.create_date)
                                    $("#expire_date").val(inpermitPro.expire_date)
                                }

                            },
                        error: function(xhr){
                                alert(xhr.status+' '+xhr.statusText);
                            }
                    });
                }

            }catch(e){
                alert(e.message);
            }

        });

        $(document).on("change", ".runIDedit", function(e){
            try{
                Public_Price = $("#Public_Price").val()
                product_id = $(this).attr("proId");
                runID =$(this).val();
                if(product_id && runID){
                    $.ajax({
                        url: "{{ url("dashboard/inpermit/") }}/" +product_id+"/"+runID,
                        type: 'GET',
                        cache:false,
                        success: function(inpermitPro){
                                if(inpermitPro == false){
                                    $('#Public_Price'+product_id).removeAttr("disabled")
                                    $('#Buy_Price'+product_id).removeAttr("disabled")
                                    $('#create_date'+product_id).removeAttr("disabled")
                                    $('#expire_date'+product_id).removeAttr("disabled")

                                }else{
                                    $('#Public_Price'+product_id).attr('disabled', 'disabled')
                                    $('#Buy_Price'+product_id).attr('disabled', 'disabled')
                                    $('#create_date'+product_id).attr('disabled', 'disabled')
                                    $('#expire_date'+product_id).attr('disabled', 'disabled')

                                    Public_Price = (inpermitPro.Public_Price)? inpermitPro.Public_Price :  $("#product_id").attr('Public_Price');
                                    $("#Public_Price").val(Public_Price)
                                    $("#Buy_Price"+product_id).val(inpermitPro.Buy_Price)
                                    $("#create_date"+product_id).val(inpermitPro.create_date)
                                    $("#expire_date"+product_id).val(inpermitPro.expire_date)
                                }

                            },
                        error: function(xhr){
                                alert(xhr.status+' '+xhr.statusText);
                            }
                    });
                }

            }catch(e){
                alert(e.message);
            }

        });
    })
</script>
@endsection


