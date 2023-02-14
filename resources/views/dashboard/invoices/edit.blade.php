@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل فاتورة رقم: <a href="{{ route('invoices.show', [$invoice->id]) }}"><span class="badge badge-pill badge-cyan ml-auto">{{ $invoice->invoice_code }}</span></a> </h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('vouchers.show', $invoice->voucher->id) }}" class="btn btn-primary float-right">إذن صرف رقم: {{ $invoice->voucher->voucher_code }} </a>
                @if(Auth::user()->voucher && Auth::user()->can('Create Invoices') && !Auth::user()->voucher->user_keeper_return_id && !Auth::user()->voucher->user_accountant_return_id && Auth::user()->voucher->voucher_status == 3)
                <a href="{{ route('invoices.create', Auth::user()->voucher->id) }}" class="btn mx-3 btn-info float-right">أضف جديد</a>
                @endif
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- /End Bread crumb and right sidebar toggle Emad test -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">تعديل فاتورة رقم: <a href="{{ route('invoices.show', [$invoice->id]) }}"><span class="badge badge-pill badge-cyan ml-auto">{{ $invoice->invoice_code }}</span></a> </h6>
                    <hr>
                    @php $cols = 3; @endphp
                    @include('dashboard.invoices.editModel')
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
        $('select#client_id').selectpicker();
        function changeProductId(selectRunID="") {
            voucher_id = {{ $invoice->voucher->id }}
            product_id = $("#product_id").val()

            client_id = $("#client_id").val()
            if(!client_id) client_id = ""

            if(product_id && runID && voucher_id && client_id){
                urlStr = "{{ url("dashboard/invoice/getrunid") }}/" +product_id+"/"+voucher_id+ "/" +  client_id + "/" +  {{ $invoice->id }}
                $.ajax({
                    url: urlStr,
                    type: 'GET',
                    cache:false,
                    success: function(data){
                            if(data == false){
                            }else{
                                $("#discount").attr('paid_discount', data.paid_discount)
                                $("#discount").attr('due_discount', data.due_discount)

                                if(data.allow_due_product){
                                    alert(data.allow_due_product)
                                    $("#product_id").val('')
                                    $("#product_id").focus()
                                    return false;
                                }
                                $("div .runID").replaceWith(data.select_runIDs)

                                if(selectRunID){
                                    $('#runID').val(selectRunID)
                                }
                                if($('#runID').val()){
                                    voucher_id = {{ $invoice->voucher->id }}
                                    runID=$("#runID").val()
                                    product_id = $("#product_id").val()

                                    if(product_id && runID && voucher_id){
                                        $.ajax({
                                            url: "{{ url("dashboard/invoice/") }}/" +product_id+"/"+runID+"/"+{{ $invoice->id }}+"/edit/true",

                                            type: 'GET',
                                            cache:false,
                                            success: function(data){
                                                    if(data == false){
                                                        $('#avliable').val(0)
                                                        $('#invoice_quantity').attr('max', 0)
                                                    }else{
                                                        $('#avliable').val(data.net_q)
                                                        $('#invoice_quantity').attr('max', data.Max_Discount)
                                                        $('#invoice_quantity').attr('min', data.Min_Discount)
                                                        $('#discount').attr('max', data.Max_Discount)
                                                        $('#discount').attr('min', data.Min_Discount)
                                                        $('#invoice_quantity').attr('Public_Price', data.Public_Price)
                                                        $("#Public_Price").text(data.Public_Price)
                                                        if(!selectRunID){
                                                            $(".inptutNull").val('')
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
                        },
                    error: function(xhr){
                            alert(xhr.status+' '+xhr.statusText);
                        }
                });
            }
        }


        $(document).on("click", "#cancelEdit", function(e){
            $("#inpermitAdd").html('<i class="fa fa-plus-circle"></i>أضف')
            $("#inpermitAdd").attr('editAdd', 'add')
            $(".product_id").val('')
            $(".runID").val('')
            $(".avliable").val('')
            $("#cancelEdit").hide()
            $(".inptutNull").val('')
            $("div.product_id").removeClass('col-md-1').addClass('col-md-2')
        })

        $(document).on("click", ".productEdit", function(e){
            product_id = $(this).attr('product_id')
            runID = $(this).attr('runID')
            invoice_quantity = $(this).attr('invoice_quantity')
            bounce = $(this).attr('bounce')
            discount = $(this).attr('discount')
            pay_price = $(this).attr('pay_price')
            pay_quantity = $(this).attr('pay_quantity')
            paid_price = $(this).attr('paid_price')
            paid_next = $(this).attr('paid_next')

            $("#product_id").val(product_id)
            changeProductId(runID)

            $("#runID").val(runID)
            $("#invoice_quantity").val(invoice_quantity)
            $("#bounce").val(bounce)
            $("#discount").val(discount)
            $("#pay_price").val(pay_price)
            $("#pay_quantity").val(pay_quantity)
            $("#paid_price").val(paid_price)
            $("#paid_next").val(paid_next)

            $("#inpermitAdd").html('<i class="fa fa-save"></i>حفظ')
            $("#inpermitAdd").attr('editAdd', 'edit')
            $("#cancelEdit").show()
            $("div.product_id").removeClass('col-md-2').addClass('col-md-1')
            e.preventDefault()
        })

        @if(old('client_id'))
            $.ajax({
                url: "{{ url("dashboard/client/getOverPriceSum") }}/" + {{old('client_id')}},

                type: 'GET',
                cache:false,
                success: function(data){
                        if(data !=0 && data == false){
                            $("#client_balance").text(0)
                        }else{
                            $("#client_balance").text(data)
                            totalPaid = 0
                            $(".paid_priceSum").each(function(){
                                totalPaid += parseFloat($(this).val())
                            })
                            minClientBalance = (data>totalPaid)? totalPaid : data;
                            totalPaid -= minClientBalance
                            $("#totalPaid").val(totalPaid)

                            client_pay = ($("#client_pay").val())? parseFloat($("#client_pay").val()) : 0;
                            minPay = 5 * parseInt(totalPaid/5)
                            $("#client_pay").attr("min", minPay)
                            client_balance_diff = (client_pay - totalPaid).toFixed(2)
                            if(minPay<=client_pay){
                                $("#client_balance_diff").text(client_balance_diff)
                            }else{
                                $("#client_pay").val()
                            }

                        }
                        setTotalPaid()
                    },
                error: function(xhr){
                        alert(xhr.status+' '+xhr.statusText);
                    }
            });
        @endif

        function checkQuantitys(changeId = "bounce") {
            bounce = parseInt($("#bounce").val())
            invoice_quantity = parseInt($("#invoice_quantity").val())
            avliable = parseInt($("#avliable").val())
            if((bounce + invoice_quantity)>avliable){
                alert("مجموع البونص والكمية لا يمكن أن يكون أكبر من المتاح")
                $("#"+changeId).val()
                $("#"+changeId).focus()
                return true;
            }else{
                return false;
            }
        }

        function setTotalPaid(){
            totalPaid = 0
            totalRequired = 0
            totalNext = 0

            if($('#pay_types').val() == 40){
                $("#totalRequired").text('')
                $("#totalPaid").text('')
                $("#totalNext").text('')
            }

            $(".pay_priceSum").each(function(){
                totalRequired += parseFloat($(this).val())
            })
            $(".paid_priceSum").each(function(){
                totalPaid += parseFloat($(this).val())
            })
            $(".paid_nextSum").each(function(){
                totalNext += parseFloat($(this).val())
            })
            client_balance = parseFloat($("#client_balance").text()).toFixed(2);
            minClientBalance = (client_balance>totalPaid)? totalPaid : client_balance;
            totalPaid -=minClientBalance
            if($('#pay_types').val() == 40){
                $("#totalRequired").text(totalRequired)
                totalPaid = totalRequired - client_balance
                $("#totalPaid").text(parseFloat(totalPaid).toFixed(2))
                $("#totalNext").text(parseFloat(totalPaid).toFixed(2))
            }else{
                $("#totalPaid").text(totalPaid)
                $("#totalRequired").text(totalRequired)
                $("#totalNext").text(totalNext)
            }

            client_pay = ($("#client_pay").val())? parseFloat($("#client_pay").val()) : 0;
            minPay = 5 * parseInt(totalPaid/5)
            $("#client_pay").attr("min", minPay)
            client_balance_diff = (client_pay - totalPaid).toFixed(2)
            if(minPay<=client_pay){
                $("#client_balance_diff").text(client_balance_diff)
            }else{
                $("#client_pay").val()
            }


        }

        setTotalPaid()


        lastCount = parseInt($('#productsTable tbody tr').length) -1 ;

        $(document).on("click", "#inpermitAdd", function(e){
            pay_quantity = parseInt($("#pay_quantity").val())
            invoice_quantity = parseInt($("#invoice_quantity").val())

            if(!$("#product_id").val()){
                alert("من فضلك إختر منتج")
                return ;
            }
            if(!$("#runID").val()){
                alert("من فضلك إختر رقم تشغيلة")
                return ;
            }
            if(!$("#invoice_quantity").val()){
                alert("من فضلك اختر كمية")
                return ;
            }
            if(!$("#bounce").val()){
                $("#bounce").val(0)
            }

            if(!$("#discount").val() || parseFloat($("#discount").val())<0 || parseFloat($("#discount").val())>100){
                alert("من فضلك ادخل الخصم بشكل صحيح")
                return ;
            }

            if(!$("#pay_price").val()){
                alert("")
                return ;
            }

            if(!$("#pay_quantity").val()){
                $("#pay_quantity").val(0)
            }

            if(!$("#paid_price").val()){
                alert("من فضلك أدخل قيمة السداد")
                return ;
            }

            if(!$("#paid_next").val()){
                alert("من فضلك أدخل الأجل")
                return ;
            }

            if(check_discount()){
                return;
            }

            if(pay_quantity>invoice_quantity || pay_quantity<0){
                alert("كمية السداد يجب ان تكون أقل من أو تساوي الكمية")
                return ;
            }

            if(!$("#runID").val() || !parseInt($("#product_id").val())
            || !parseInt($("#invoice_quantity").val())
            || parseInt($("#invoice_quantity").val()) <1){
                e.preventDefault();
                alert("من فضلك اختار صنف وكمية موجبة أقل من 9999999..! وباقي بيانات الصنف بشكل صحيح.")
                return ;
            }

            if(checkQuantitys()) {
                e.preventDefault();
                return;
            }

            id_runID = $("#product_id").val()+ "_" + $("#runID").val()
            editAdd = $(this).attr('editAdd')

            if($("#row"+ id_runID).length && editAdd == 'add'){
                e.preventDefault();
                alert(" هذا الصنف موجود بالفعل في الفاتورة يمكنك تغيير كميته بدل من إضافته مرة أخري.!")
                return ;
            }else if($("#row"+ id_runID).length){
                $("#row"+ id_runID).remove()
                $("#cancelEdit").hide()
                $("#inpermitAdd").html('<i class="fa fa-plus-circle"></i>أضف')
                $("div.product_id").removeClass('col-md-1').addClass('col-md-2')
            }

            $newRow = '<tr id="row'+  id_runID +'" class="productItem">'
                        + '<td>'+ lastCount + '</td>'
                        + '<td> <input type="hidden" name="product_ids[]" value="'+ $("#product_id").val() +'">' + $("#product_id option:selected").text() + '</td>'
                        + '<td> <input type="text" name="runIDs[]" value="'+ $("#runID").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td> <input type="number" name="invoice_quantitys[]" value="'+ $("#invoice_quantity").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td> <input type="number" name="bounces[]" value="'+ $("#bounce").val() +'" class="form-control" step="0.01" readonly></td>'
                        + '<td> <input type="number" name="discounts[]" value="'+ $("#discount").val() +'" class="form-control" readonly></td>'
                        + '<td> <input type="number" name="pay_prices[]" value="'+ $("#pay_price").val() +'" class="form-control pay_priceSum" readonly></td>'
                        + '<td> <input type="number" name="pay_quantitys[]" value="'+ $("#pay_quantity").val() +'" class="form-control pay_quantityItem" readonly'
                        + ' discount="'+ $("#discount").val() +'" invoice_quantity="'+ $("#invoice_quantity").val() +'" Public_Price="'+ $("#Public_Price").text() +'"></td>'
                        + '<td> <input type="number" name="paid_prices[]" value="'+ $("#paid_price").val() +'" class="form-control paid_priceSum" readonly></td>'
                        + '<td> <input type="number" name="paid_nexts[]" value="'+ $("#paid_next").val() +'" class="form-control paid_nextSum" readonly></td>'
                        + '<td> <a href="#" delId="row'+  id_runID +'" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a>'
                            + '<a href="#"'
                            + 'product_id="'+ $("#product_id").val() +'"'
                            + 'runID="'+ $("#runID").val() +'"'
                            + 'invoice_quantity="'+ $("#invoice_quantity").val() +'"'
                            + 'bounce="'+ $("#bounce").val() +'"'
                            + 'discount="'+ $("#discount").val() +'"'
                            + 'pay_price="'+ $("#pay_price").val() +'"'
                            + 'pay_quantity="'+ $("#pay_quantity").val() +'"'
                            + 'paid_price="'+ $("#paid_price").val() +'"'
                            + 'paid_next="'+ $("#paid_next").val() +'"'
                            + 'delId="row'+  id_runID +'" class="productEdit"><i class="fas fa-edit delEdit"></i></a>'
                        + '</td>'
                     +'<tr>';

            lastCount++;
            totalPaid += parseFloat($("#paid_price").val())
            $("#invoice_quantity").val('')

            $("#product_id").val('')
            $("#runID").val('')
            $("#rest").val('')
            $("#avliable").val('')
            tableBody = $("#productsTable tbody");
            tableBody.append($newRow);

            $(".inptutNull").val('')
            $("#client_pay").val('')
            $("#client_balance_diff").text('')
            $("#product_id").focus()
            setTotalPaid()
            if(editAdd == "edit"){
                $("#inpermitAdd").attr('editAdd', 'add')
            }

            // $("#client_pay").val('')
            // $("#Public_Price").text('')
        });


        $(document).on("click", ".prodcutDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            setTotalPaid()
            e.preventDefault();
        });

        $(document).on("change", "#pay_types", function(e){
            $url = "{{ url('dashboard/invoices/' . $invoice->id . "/edit") }}" + "/" + $("#pay_types").val()
            window.location.replace($url);
        });

        $(document).on("change", "#product_id", function(e){
            $("#runID").val('')
            $("#avliable").val('')
            $("#Public_Price").html('')

            $("#discount").attr('paid_discount', '')
            $("#discount").attr('due_discount', '')

            if(!$("#product_id").val()){
                alert("من فضلك اختر منتج!")
                $(this).val('')
                $("#product_id").focus()

            }else{
                changeProductId()
            }
        });

        $(document).on("change", "#runID", function(e){

            if(!$("#product_id").val()){
                alert("من فضلك اختر منتج!")
                $(this).val('')
                $("#product_id").focus()
            }else{
                voucher_id = {{ $invoice->voucher->id }}
                runID=$("#runID").val()
                product_id = $("#product_id").val()

                if(product_id && runID && voucher_id){
                    $.ajax({
                        url: "{{ url("dashboard/invoice/") }}/" +product_id+"/"+runID+"/"+{{ $invoice->id }}+"/edit/true",

                        type: 'GET',
                        cache:false,
                        success: function(data){
                                if(data == false){
                                    $('#avliable').val(0)
                                    $('#invoice_quantity').attr('max', 0)
                                }else{
                                    $('#avliable').val(data.net_q)
                                    $('#invoice_quantity').attr('max', data.Max_Discount)
                                    $('#invoice_quantity').attr('min', data.Min_Discount)
                                    $('#discount').attr('max', data.Max_Discount)
                                    $('#discount').attr('min', data.Min_Discount)
                                    $('#invoice_quantity').attr('Public_Price', data.Public_Price)
                                    $("#Public_Price").text(data.Public_Price)
                                    $(".inptutNull").val('')
                                }
                            },
                        error: function(xhr){
                                alert(xhr.status+' '+xhr.statusText);
                            }
                    });
                }
            }
        });




        function setInputs(changeId){
            if(!$("#runID").val()){
                alert(" من فضلك اختر رقم تشغيله!")
                $("#runID").focus()
                $('.inptutNull').val('')
            }

            if(!$("#product_id").val()){
                alert(" من فضلك اختر منتج!")
                $("#product_id").focus()
                $('.inptutNull').val('')
            }

            avliable = parseInt($("#avliable").val())
            bounce = parseInt($("#bounce").val())
            invoice_quantity = ($("#invoice_quantity").val())? parseInt($("#invoice_quantity").val()) : 0;
            discount = ($("#discount").val())? parseFloat($("#discount").val()) : 0;
            pay_quantity = ($("#pay_quantity").val())? parseInt($("#pay_quantity").val()) : 0;
            Public_Price = ($("#invoice_quantity").attr("Public_Price"))? parseFloat($("#invoice_quantity   ").attr("Public_Price")) : 0;

            if(changeId == 'bounce'){
                if(bounce<0 || bounce>avliable){
                    alert("البونص لا يمكن ان يكون اكبر من المتاح او اقل من الصفر!!")
                    $("#bounce").focus()
                }else{
                    checkQuantitys(changeId)
                }
            }


            if(changeId == 'discount'){
                min = $("#discount").attr('min')
                max = $("#discount").attr('max')
                if(discount<0 || discount>max){
                    alert("خصم البيع غير موافق لسياسات البيع!")
                    $("#discount").focus()
                }
            }

            if(changeId == 'pay_quantity'){
                if(parseInt($('#pay_quantity').val())> parseInt($('#invoice_quantity').val())
                    || parseInt($('#pay_quantity').val())<0){
                    alert ($('#invoice_quantity').val() + "! كمية السداد يجب ان تكون اقل من الكمية المطلوبة و أكبر من الصفر!" + $('#invoice_quantity').val())
                    $('#pay_quantity').val('')
                    $('#pay_quantity').focus()
                }
            }

            if(changeId == 'invoice_quantity'){
                if(!checkQuantitys(changeId)){
                    if(parseInt($('#invoice_quantity').val())> parseInt($('#avliable').val())
                    ||  parseInt($('#invoice_quantity').val())<0){
                        alert ($('#invoice_quantity').val() + "! الكمية يجب ان تكون اقل من المتاح و أكبر من الصفر!" + $('#avliable').val())
                        $('#invoice_quantity').val('')
                        $('#invoice_quantity').focus()
                    }

                    if($("#pay_quantity").val()>$("#invoice_quantity").val() || $("#pay_quantity").val()<0){
                        alert("الكمية يجب ان تكون اكبر من أو تساوي كمية السداد")
                        $("#invoice_quantity").val('')
                        $("#invoice_quantity").focus()
                    }
                }

            }

            pay_price_val = Public_Price*(100-discount) * invoice_quantity / 100

            $("#pay_price").val(pay_price_val.toFixed(2))

            paid_price_val = Public_Price*(100-discount) * pay_quantity / 100
            paid_next_val = Public_Price*(100-discount) * (invoice_quantity-pay_quantity) / 100
            $("#paid_price").val(paid_price_val.toFixed(2))
            $("#paid_next").val(paid_next_val.toFixed(2))
        }

        $(document).on("change", "#invoice_quantity", function(e){
            setInputs("invoice_quantity")
        })

        $(document).on("change", "#bounce", function(e){
            setInputs("bounce")
        });

        $(document).on("change", "#discount", function(e){
            setInputs("invoice_quantity")
        })

        function check_discount(){
            $msg = "";
            $pay_type = $("#pay_types").val()
            $paid_discount = parseFloat($("#discount").attr('paid_discount')).toFixed(2)
            $due_discount = parseFloat($("#discount").attr('due_discount')).toFixed(2)
            $discount = parseFloat($("#discount").val()).toFixed(2)

            if($pay_type == 10){
                if($discount>$paid_discount){
                    $msg = " هذه الخصم أكبر من الخصم المسموح به في حالة السداد النقدي"
                }
            }else if($pay_type == 20){
                if($discount>$due_discount){
                    $msg = " هذه الخصم أكبر من الخصم المسموح به في حالة السداد الأجل"
                }
            }else if($pay_type == 30){
                if($("#pay_quantity").val() == $("#invoice_quantity").val()){
                    if($discount>$paid_discount){
                        $msg = " هذه الخصم أكبر من الخصم المسموح به في حالة السداد النقدي"
                    }
                }else{
                    if($discount>$due_discount){
                        $msg = " هذه الخصم أكبر من الخصم المسموح به في حالة السداد الأجل"
                    }
                }

            }else if($pay_type == 40){
                if($discount>$due_discount){
                    $msg = " هذه الخصم أكبر من الخصم المسموح به في حالة السداد الأجل"
                }
            }
            if($msg){
                alert($msg)
                $("#discount").val('')
            }
            return $msg
        }

        $(document).on("change", "#discount", function(e){
            check_discount()
            setInputs("invoice_quantity")
        })

        $(document).on("change", "#pay_quantity", function(e){
            setInputs("pay_quantity")
        });

        $(document).on("change", "#client_pay", function(e){
            $pay_type = $('#pay_types').val()
            if($pay_type == 40){
                $("#client_pay").attr('min', '0')
                client_balance = parseFloat($('#client_balance').text())
                client_pay = parseFloat($(this).val()) + client_balance

                $(".productItem").each(function () {
                    Public_Price = parseFloat($(this).find(".pay_quantityItem").attr('Public_Price'))
                    discount = parseFloat($(this).find(".pay_quantityItem").attr('discount'))
                    invoice_quantity = parseInt($(this).find(".pay_quantityItem").attr('invoice_quantity'))
                    unit_price = (100-discount)/100*Public_Price
                    canPay_q = parseInt(client_pay/unit_price)
                    canPay_q = (canPay_q<=invoice_quantity)? canPay_q : invoice_quantity;
                    $(this).find(".pay_quantityItem").val(canPay_q)
                    payItemPrice = canPay_q * unit_price
                    paid_nextSum = invoice_quantity * unit_price - payItemPrice
                    $(this).find('.paid_priceSum').val(parseFloat(payItemPrice).toFixed(2))
                    $(this).find('.paid_nextSum').val(parseFloat(paid_nextSum).toFixed(2))
                    client_pay -= payItemPrice
                })
                totalRequired = parseFloat($("#totalRequired").text())
                totalNext = totalRequired - client_pay
                $("#totalNext").text(parseFloat(totalNext).toFixed(2))
                $("#client_balance_diff").text(parseFloat(client_pay).toFixed(2))
            }else{
                client_pay = parseFloat($("#client_pay").val())
                totalPaid = 0
                $(".paid_priceSum").each(function(){
                    totalPaid += parseFloat($(this).val())
                })

                client_balance = parseFloat($("#client_balance").text()).toFixed(2);
                minClientBalance = (client_balance>totalPaid)? totalPaid : client_balance;
                totalPaid -=minClientBalance

                minPay = 5 * parseInt(totalPaid/5)
                $("#client_pay").attr("min", minPay)
                client_balance_diff = (client_pay - totalPaid).toFixed(2)
                if(minPay<=client_pay){
                    $("#client_balance_diff").text(client_balance_diff)
                }else{
                    alert("عذرا قيمة السداد يجب ان تكون اكبر من أو تساوي: " +  minPay)
                    $("#client_pay").val()
                    $("#client_pay").focus()
                }
            }
        });

        $(document).on("change", "#client_id", function(e){
            $client_id = $(this).val();
            if($client_id){
                $.ajax({
                    url: "{{ url("dashboard/client/getOverPriceSum") }}/" +$client_id,

                    type: 'GET',
                    cache:false,
                    success: function(data){

                        if(data == false){
                                $("#client_balance").text(0)
                            }else{
                                if(data.is_client_due_limit != false){
                                    alert("العميل تخطي الحد المسموح به للأجل، لا يمكن إنشاء فاتورة لهذا العميل.")
                                    $("#client_id").val('')
                                    $(".client_id .filter-option-inner-inner").html('العميل*')
                                }
                                $("#client_balance").text(data.get_overPrice_sum)
                            }
                            setTotalPaid()
                        },
                    error: function(xhr){
                            alert(xhr.status+' '+xhr.statusText);
                        }
                });
            }else{
                $("#client_balance").text(0)
            }
        });



        // Start of Compress image locallay befor uploadeng
        const MAX_WIDTH = 720;
        const MAX_HEIGHT = 1280;
        const MIME_TYPE = "image/jpeg";
        const QUALITY = 1;

        const input = document.getElementById("image");
        input.onchange = function(ev) {
            $("#previewInvoice").html('')
            const file = ev.target.files[0]; // get the file
            const blobURL = URL.createObjectURL(file);
            const img = new Image();
            img.src = blobURL;

            img.onerror = function() {
                URL.revokeObjectURL(this.src);
                // Handle the failure properly
                console.log("Cannot load image");
            };
            img.onload = function() {
                URL.revokeObjectURL(this.src);
                const [newWidth, newHeight] = calculateSize(img, MAX_WIDTH, MAX_HEIGHT);
                const canvas = document.createElement("canvas");
                canvas.width = newWidth;
                canvas.height = newHeight;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, newWidth, newHeight);
                canvas.toBlob(
                    (blob) => {
                        // Handle the compressed image. es. upload or save in local state
                        displayInfo('Original file', file);

                        displayInfo('Compressed file', blob);
                    },
                    MIME_TYPE,
                    QUALITY
                );
                document.getElementById("previewInvoice").append(canvas);
                $("#previewInvoice canvas").attr('id', 'canvasId')

            };


        };

        function calculateSize(img, maxWidth, maxHeight) {
            let width = img.width;
            let height = img.height;

            // calculate the width and height, constraining the proportions
            if (width > height) {
                if (width > maxWidth) {
                    height = Math.round((height * maxWidth) / width);
                    width = maxWidth;
                }
            } else {
                if (height > maxHeight) {
                    width = Math.round((width * maxHeight) / height);
                    height = maxHeight;
                }
            }
            return [width, height];
        }

        // Utility functions for demo purpose

        function displayInfo(label, file) {
            const p = document.createElement('p');
            p.innerText = `${label} - ${readableBytes(file.size)}`;
            document.getElementById('previewInvoice').append(p);
        }

        function readableBytes(bytes) {
            const i = Math.floor(Math.log(bytes) / Math.log(1024)),
                sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
        }


        $("#loginform").on('submit', function (e) {

            var canvas= document.getElementById('canvasId')
            var dataURL = canvas.toDataURL()
            // alert (dataURL.length)
            $("#imgData").val(dataURL)
        })

        // End of Compress image locallay befor uploadeng

    })
</script>
@endsection


