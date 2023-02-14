@extends('dashboard.master', ['form' => 1])

@section('content')
<?php $start_time = microtime(true); ?>
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h4 class="text-themecolor">أمر تحويل بين المخاذن</h4>
            </div>
            <div class="col-md-7 align-self-center text-right">
                <div class="d-flex justify-content-end align-items-center">
                    <a href="{{ route('transfers.index') }}" class="btn btn-primary float-right">كل أوامر التحويل </a>
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
                        <h6 class="card-title">أمر تحويل : </h6>
                        <hr>
                        @php $cols = 3; @endphp
                        @include('dashboard.transfers.createModel')
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
    <script>
        $('document').ready(function() {
            var voucherall = false;
            @if(Auth::user()->can(['Transfer_all_store']))
            document.querySelector('.voucherall').onchange=function(){
                if(this.checked){
                    document.querySelector('.relationElements').style.display = 'none';
                    document.querySelector('.table-responsive').style.display = 'none';
                    voucherall = true
                }else{
                    document.querySelector('.relationElements').style.display = 'flex';
                    document.querySelector('.table-responsive').style.display = 'flex';
                    voucherall = false
                }
            }
            @endif
            
            var stock = null;

            lastCount = parseInt($("#lastCount").val()) + 1;

            $(document).on("click", "#inpermitAdd", function(e) {

                if (!$("#runID").val() || !$("#product_id").val() || !$("#Quantity").val() || $("#Quantity")
                    .val() < 1) {
                    e.preventDefault();
                    alert("من فضلك اختار صنف وكمية موجبة أقل من 9999999..! وباقي بيانات الصنف بشكل صحيح.")
                    return;
                }

                id_runID = $("#product_id").val() + "_" + $("#runID").val().replace('.', '')

                if ($("#row" + id_runID).length) {
                    e.preventDefault();
                    alert(
                        " هذا الصنف موجود بالفعل في اذن التحويل يمكنك تغيير كميته بدل من إضافته مرة أخري.!"
                    )   
                    return;
                }

                $newRow = '<tr id="row' + id_runID.replace('.', '') + '">' +
                    '<td data-th="#">' + lastCount + '</td>' +
                    '<td data-th="الصنف"> <input type="hidden" name="product_ids[]" value="' + $(
                        "#product_id").val() + '">' + $("#product_id option:selected").text() + '</td>' +
                    '<td data-th="{{ trans('validation.attributes.runID') }}"> <input type="text" name="runIDs[]" value="' +
                    $("#runID").val() + '" class="form-control" min="0" max="99999999" readonly></td>' +
                    '<td data-th="الكمية"> <input type="number" name="quantities[]" value="' + $(
                        "#Quantity").val() +
                    '" class="form-control" min="0" max="99999999" readonly></td>' +
                    '<td data-th="حذف"> <a href="#" delId="row' + id_runID.replace('.', '') +
                    '" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>' +
                    '<tr>';

                lastCount++;

                $("#Quantity").val('')

                $("#product_id").val('')
                $("#runID").val('')
                $("#rest").val('')
                $("#avliable").val('')
                tableBody = $("#productsTable tbody");
                tableBody.append($newRow);

                $("#product_id").focus()

            });

            $(document).on("click", ".prodcutDelete", function(e) {
                delId = $(this).attr('delId');
                $("#" + delId).remove()
                e.preventDefault();
            });

            $(document).on("change", "#runID", function(e) {

                if (!$("#product_id").val()) {
                    alert("من فضلك اختر منتج!")
                    $(this).val('')
                    $("#product_id").focus()

                } else {
                    $('#avliable').val('')
                    $('#Quantity').val('')
                    $('#Quantity').attr('max', 0)
                    $('#rest').val('')

                    store_id = $("#from_store_id").val()
                    runID = $("#runID").val()
                    product_id = $("#product_id").val()

                    if (product_id && runID && store_id) {
                        var q = $('option:selected', this).attr('q');
                        var Public_Price = $('option:selected', this).attr('Public_Price');
                        
                        $('#avliable').val(q)
                        $('#Public_Price_span').html(Public_Price)
                        $('#Quantity').attr('max', q)
                        $('#rest').val('')
                    }
                }
            });

            $(document).on("change", "#product_id", function(e) {

                store_id = $("#from_store_id").val()
                runID = $("#runID").val()
                product_id = $("#product_id").val()

                $("#runID").val('')
                $('#avliable').val('')
                $('#Quantity').val('')
                $('#Quantity').attr('max', 0)
                $('#rest').val('')


                if (store_id) {

                    pros = stock.filter((s) => s.product_id == product_id)


                    select_runID = '<select id="runID" class="form-control runID" name="runID">';
                    select_runID += '<option  value="">التشغيلة</option>';
                    selected = (pros.length < 2) ? 'selected' : '';
                    avliable = '';
                    pros.forEach(pro => {
                        avliable = (pros.length < 2) ? pro.store_q_net : '';
                        Public_Price = (pros.length < 2) ? pro.Public_Price : '';
                            select_runID += '<option Public_Price="' + pro.Public_Price + '" q="' + pro.store_q_net + '"  ' + selected +
                                ' value="' + pro.runID + '">' + pro.runID + '</option>';
                    });
                    select_runID += '</select>';

                    $("select#runID").replaceWith(select_runID)
                    $('#avliable').val(avliable)
                    // $('#Quantity').val(avliable)
                    $('#inpermitAdd').focus()
                    $('#Public_Price_span').html(Public_Price)
                    $('#Quantity').attr('max', avliable ? avliable : 0)
                    $('#rest').val('')
                }
            });

            $(document).on("change", "#Quantity", function(e) {
                if (!$("#product_id").val()) {
                    alert("من فضلك اختر منتج!")
                } else {
                    rest = $('#avliable').val() - $(this).val();
                    if (rest < 0 || rest >= $('#avliable').val()) {
                        alert(" الكمية يجب ان تكون اقل من المتاح و أكبر من الصفر!")
                        $(this).val('')
                        $('#rest').val('')
                        $(this).focus()
                    } else
                        $('#rest').val(rest)
                }

            })

            $(document).on("change", "#from_store_id", function(e) {
                loading('product_id', 'الصنف', 3)
                $("#product_id").val('')
                $("#runID").val('')
                $('#avliable').val('')
                $('#Quantity').val('')
                $('#Quantity').attr('max', 0)
                $('#rest').val('')
                from_store_id_chaneg()
            })

            function from_store_id_chaneg() {
                from_store_id = $("#from_store_id").val()
                if (from_store_id) {
                    $.ajax({
                        url: "{{ url('dashboard/transfer/fromto') }}/" + from_store_id,
                        type: 'GET',
                        cache: false,
                        success: function(data) {
                            if (data == false) {

                            } else {
                                s_pros = uniqueByKey(data.stock, 'product_id')

                                select_product_id =
                                    '<select id="product_id" class="form-control product_id" name="product_id">';
                                select_product_id += '<option  value="">الصنف</option>';
                                s_pros.forEach(pro => {
                                    select_product_id += '<option value="' + pro.product_id +
                                        '">' + pro.Product_Name + '</option>';
                                });
                                select_product_id += '</select><p>السعر: <span id="Public_Price_span"></span></p>';


                                $("#proLoading").replaceWith(select_product_id);
                                $("#t1").html(data.t1.toFixed(2));
                                stock = data.stock;
                            }

                        },
                        error: function(xhr) {
                            alert(xhr.status + ' ' + xhr.statusText);
                        }
                    });
                }
            }

            from_store_id_chaneg()


            function loading(classN, label, col = 2) {
                $("." + classN).replaceWith('<div class="col-md-' + col + ' ' + classN +
                    '"><div class="form-group "> <label for="' + classN + '">' + label +
                    '</label>  <p id="proLoading" style="display:block;font-size:12px;"> انتظر 10 ثواني لحين جرد المخزن <img src="https://thumbs.gfycat.com/OddRapidAngwantibo-size_restricted.gif" style="width:50px; height:50px; display:inline;"></p></div></div>'
                )
            }

            function uniqueByKey(array, key) {
                return [...new Map(array.map((x) => [x[key], x])).values()];
            }

        })
    </script>
@endsection
