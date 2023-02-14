@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">فاتورة عميل</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                @if(Auth::user()->can(['CRUD Clients']) || Auth::user()->can(['add_first_client']))
                <a href="{{ route('clients.create') }}" class="btn btn-info float-right mx-2">أضف عميل جديد</a>
                @endif
                <a href="{{ route('vouchers.show', $voucher->id) }}" class="btn btn-primary float-right">إذن صرف رقم: {{ $voucher->voucher_code }} </a>
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
                    <h6 class="card-title">فاتورة عميل: </h6>
                    <hr>
                    @php $cols = 3; @endphp
                    @include('dashboard.invoices.createModel')
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

        //Will store voucher product quantities
        var voucher_quantities = "";
        $.ajax({
                url: "{{ route('vouchers.quantities', ['voucher'=>$voucher->id]) }}",
                type: 'GET',
                cache:false,
                success: function(data){
                    voucher_quantities = data;
                },
                error: function(xhr){
                    alert(xhr.status+' '+xhr.statusText);
                }
        });

        $('select#client_id').selectpicker();

        function totalTR(totalRequired, totaleInoice, totalNext) {
            $(".totalTr").remove()
            $lastTr = '<tr class="totalTr">'
                    +'<th colspan="6">إجمــــالي</th>'
                    +'<td <td data-th="القيمة"><span style="color: #000;" id="totalRequired">'+totalRequired+'</span></td>'
                    +'<td></td>'
                    +'<td <td data-th="سداد"><span style="color: blue;" id="totalInoice">'+totaleInoice+'</span></td>'
                    +'<td <td data-th="أجل"><span style="color: red;" id="totalNext">'+totalNext+'</span></td>'
                    +'<td></td>'
                    +'</tr>'
            $('#productsTable').find('tr:last').prev().after($lastTr);
        }

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
                                $("#client_balance_diff").text(parseFloat(client_balance_diff).toFixed(2))
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
            if($('#pay_types').val() == 40){
                $("#totalRequired").text('')
                $("#totalPaid").text('')
                $("#totalNext").text('')
            }

            let totalPaid = 0
            let totalRequired = 0
            let totalNext = 0
            $(".pay_priceSum").each(function(){
                totalRequired += parseFloat($(this).val())
            })
            $(".paid_priceSum").each(function(){
                totalPaid += parseFloat($(this).val())
            })
            $(".paid_nextSum").each(function(){
                totalNext += parseFloat($(this).val())
            })

            totalNext = parseFloat(totalNext).toFixed(2)
            totalPaid = parseFloat(totalPaid).toFixed(2)
            client_balance = parseFloat($("#client_balance").text()).toFixed(2);

            minClientBalance = (parseFloat(client_balance)>parseFloat(totalPaid))? totalPaid : client_balance;
            totalPaid -=minClientBalance

            if($('#pay_types').val() == 40){

                totalPaid = parseFloat(totalRequired) - parseFloat(client_balance)
                totalPaid = parseFloat(totalPaid).toFixed(2)
                totalTR(totalRequired, 0, totalNext)
                $("#totalPaid").text(parseFloat(totalPaid).toFixed(2))
            }else{
                totaleInoice = parseFloat(totalPaid) + parseFloat(client_balance)
                totaleInoice = parseFloat(totaleInoice).toFixed(2)
                totalTR(totalRequired, parseFloat(totalPaid), totalNext)
                $("#totalPaid").text(parseFloat(totalPaid).toFixed(2))
            }

            client_pay = ($("#client_pay").val())? parseFloat($("#client_pay").val()) : 0;
            minPay = 5 * parseInt(totalPaid/5)
            $("#client_pay").attr("min", minPay)
            client_balance_diff = (client_pay - totalPaid).toFixed(2)
            if(minPay<=client_pay){
                if($('#pay_types').val() == 40){
                    $("#client_balance_diff").text(0)
                }else{
                    $("#client_balance_diff").text(parseFloat(client_balance_diff).toFixed(2))
                }
            }else{
                $("#client_pay").val()
            }
        }

        setTotalPaid()



        lastCount = parseInt($("#lastCount").val()) +1;

        $(document).on("click", "#inpermitAdd", function(e){

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

            id_runID = $("#product_id").val()+ "_" + $("#runID").val().replace('.', '')

            if($("#row"+ id_runID).length){
                e.preventDefault();
                alert(" هذا الصنف موجود بالفعل في الفاتورة يمكنك تغيير كميته بدل من إضافته مرة أخري.!")
                return ;
            }

            $newRow = '<tr id="row'+  id_runID.replace('.', '') +'" class="productItem">'
                        + '<td data-th="#id">'+ lastCount + '</td>'
                        + '<td data-th="الصنف"> <input type="hidden" name="product_ids[]" value="'+ $("#product_id").val() +'">' + $("#product_id option:selected").text() + '</td>'
                        + '<td data-th="{{ trans('validation.attributes.runID') }}"> <input type="text" name="runIDs[]" value="'+ $("#runID").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td data-th="الكمية"> <input type="number" name="invoice_quantitys[]" value="'+ $("#invoice_quantity").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td data-th="بونص"> <input type="number" name="bounces[]" value="'+ $("#bounce").val() +'" class="form-control" step="0.01" readonly></td>'
                        + '<td data-th="خصم"> <input type="number" name="discounts[]" value="'+ $("#discount").val() +'" class="form-control" readonly></td>'
                        + '<td data-th="القيمة"> <input type="number" name="pay_prices[]" value="'+ $("#pay_price").val() +'" class="form-control pay_priceSum" readonly></td>'
                        + '<td data-th="كمية السداد"> <input type="number" name="pay_quantitys[]" value="'+ $("#pay_quantity").val() +'" class="form-control pay_quantityItem" readonly'
                        + ' discount="'+ $("#discount").val() +'" invoice_quantity="'+ $("#invoice_quantity").val() +'" Public_Price="'+ $("#Public_Price").text() +'"></td>'
                        + '<td data-th="سداد"> <input type="number" name="paid_prices[]" value="'+ $("#paid_price").val() +'" class="form-control paid_priceSum" readonly></td>'
                        + '<td data-th="أجل"> <input type="number" name="paid_nexts[]" value="'+ $("#paid_next").val() +'" class="form-control paid_nextSum" readonly></td>'
                        + '<td data-th="حذف"> <a href="#" delId="row'+  id_runID.replace('.', '') +'" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>'
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
            $("#client_pay").val('')
            $("#Public_Price").text('')

        });

        $(document).on("click", ".prodcutDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            setTotalPaid()
            $("#client_pay").val('')
            e.preventDefault();
        });

        $(document).on("change", "#pay_types", function(e){
            $url = "{{ url('dashboard/invoice/' . $voucher->id . "/create") }}" + "/" + $("#pay_types").val()
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
                if(!$("#client_id").val()){
                    alert("من فضلك اختر عميل!")
                    $(this).val('')
                    $("#client_id").focus()
                }
                voucher_id = {{ $voucher->id }}
                product_id = $("#product_id").val()
                client_id = $("#client_id").val()

                if(product_id && voucher_id){
                    pros = voucher_quantities.filter((s) => s.product_id == product_id)
                    
                    select_runID = '<select id="runID" class="form-control runID" name="runID">';
                    select_runID += '<option  value="">التشغيلة</option>';
                    selected = (pros.length < 2) ? 'selected' : '';
                    avliable = '';
                    pros.forEach(pro => {
                        avliable = (pros.length < 2) ? pro.net_q : '';
                        Public_Price = (pros.length < 2) ? pro.Public_Price : '';
                        select_runID += '<option Public_Price="' + pro.Public_Price + '" q="' + pro.net_q + '"  ' + selected +
                            ' value="' + pro.runID + '">' + pro.runID + '</option>';
                    });
                    select_runID += '</select>';
                    $("select#runID").replaceWith(select_runID)

                    $('#avliable').val(avliable)
                    $('#Public_Price_span').html(Public_Price)
                    $('#invoice_quantity').attr('Public_Price', Public_Price)
                    $("#Public_Price").text(Public_Price)
                    $('#voucher_quantity_out').attr('max', avliable ? avliable : 0)
                    $('#avliable').val(avliable)
                    $('#invoice_quantity').attr('max', avliable)
                    $('#invoice_quantity').attr('min', 0)
                    $('#discount').attr('max', 100)
                    $('#discount').attr('min', 0)
                    $(".inptutNull").val('')                  
                }
            }
        });

        $(document).on("change", "#runID", function(e){

            if(!$("#product_id").val()){
                alert("من فضلك اختر منتج!")
                $(this).val('')
                $("#product_id").focus()

            }else{
                voucher_id = {{ $voucher->id }}
                runID=$("#runID").val()
                product_id = $("#product_id").val()

                if(product_id && runID){
                    var q = $('option:selected', this).attr('q');
                    var Public_Price = $('option:selected', this).attr('Public_Price');
                    
                    $('#avliable').val(q)
                    $('#invoice_quantity').attr('max', q)
                    $('#invoice_quantity').attr('min', 0)
                    $('#discount').attr('max', 100)
                    $('#discount').attr('min', 0)
                    $('#invoice_quantity').attr('Public_Price', Public_Price)
                    $("#Public_Price").text(Public_Price)
                    $(".inptutNull").val('')
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
                invoice_quantity_valid = false;
                if($("#pay_types").val()==10){
                    $("#pay_quantity").val($('#invoice_quantity').val())
                }else if($("#pay_types").val()==20){
                    $("#pay_quantity").val(0)
                }

                if(!checkQuantitys(changeId)){
                    if(parseInt($('#invoice_quantity').val())> parseInt($('#avliable').val())
                    ||  parseInt($('#invoice_quantity').val())<0){
                        alert ($('#invoice_quantity').val() + "! الكمية يجب ان تكون اقل من المتاح و أكبر من الصفر!" + $('#avliable').val())
                        $('#invoice_quantity').val('')
                        $('#pay_quantity').val('')
                        $('#paid_price').val('')
                        $('#pay_price ').val('')
                        $('#paid_next').val('')
                        $('#invoice_quantity').focus()
                    }else{
                        invoice_quantity_valid = true;
                    }
                }

            }

            if(invoice_quantity_valid){
                pay_quantity = ($("#pay_quantity").val())? parseInt($("#pay_quantity").val()) : 0;

                pay_price_val = Public_Price*(100-discount) * invoice_quantity / 100

                $("#pay_price").val(pay_price_val.toFixed(2))

                paid_price_val = Public_Price*(100-discount) * pay_quantity / 100
                paid_next_val = Public_Price*(100-discount) * (invoice_quantity-pay_quantity) / 100
                $("#paid_price").val(paid_price_val.toFixed(2))
                $("#paid_next").val(paid_next_val.toFixed(2))
            }

        }

        $(document).on("change", "#invoice_quantity", function(e){
            setInputs("invoice_quantity")
        })

        $(document).on("change", "#bounce", function(e){
            setInputs("bounce")
        });

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
                client_pay_org = client_pay
                totaleInoice  = 0
                totalNext = 0
                $(".productItem").each(function () {
                    Public_Price = parseFloat($(this).find(".pay_quantityItem").attr('Public_Price'))
                    discount = parseFloat($(this).find(".pay_quantityItem").attr('discount'))
                    invoice_quantity = parseInt($(this).find(".pay_quantityItem").attr('invoice_quantity'))
                    unit_price = (100-discount)/100*Public_Price
                    canPay_q = parseInt(client_pay/unit_price)

                    canPayPlus = canPay_q +1
                    canPayPlusPrice = unit_price * canPayPlus
                    canPay_q = (canPayPlusPrice<= parseFloat(client_pay).toFixed(2))? canPayPlusPrice : canPay_q;


                    canPay_q = (canPay_q<=invoice_quantity)? canPay_q : invoice_quantity;
                    $(this).find(".pay_quantityItem").val(canPay_q)
                    payItemPrice = canPay_q * unit_price
                    totaleInoice +=parseFloat(payItemPrice)
                    paid_nextSum = invoice_quantity * unit_price - payItemPrice
                    totalNext +=parseFloat(paid_nextSum)
                    $(this).find('.paid_priceSum').val(parseFloat(payItemPrice).toFixed(2))
                    $(this).find('.paid_nextSum').val(parseFloat(paid_nextSum).toFixed(2))
                    client_pay -= payItemPrice
                })
                totalRequired = parseFloat($("#totalRequired").text())
                $("#client_balance_diff").text(parseFloat(client_pay).toFixed(2))
                totalNext = parseFloat(totalNext).toFixed(2)
                totaleInoice = parseFloat(totaleInoice).toFixed(2)
                totalRequired = parseFloat(totalRequired).toFixed(2)
                totalTR(totalRequired, totaleInoice, totalNext)
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
                    $("#client_balance_diff").text(parseFloat(client_balance_diff).toFixed(2))
                }else{
                    alert("عذرا قيمة السداد يجب ان تكون اكبر من أو تساوي: " +  minPay)
                    $("#client_pay").val()
                    $("#client_pay").focus()
                }
            }
        });

        // $(document).on("focusout", "#client_pay", function(e){
        //     client_pay = ($("#client_pay").val())? parseFloat($("#client_pay").val()) : 0;
        //     payTaype_s = {{ $payType }}
        //     totalInv = parseFloat(totaleInoice).toFixed(2)
        //     console.log(totalInv, client_pay)

        //     if( totalInv < client_pay){
        //         e.preventDefault();
        //         $("#client_pay").val("")
        //         alert("عذرا لا يمكن للعيل أن يدفع أكثر من مديونية الفاتورة" +  totalInv)
        //         return;
        //     }

        // });

        $(document).on("change", "#client_id", function(e){
            $("#product_id").val('')
            $("#runID").val('')
            $("#avliable").val('')
            $("#Public_Price").html('')

            $("#discount").attr('paid_discount', '')
            $("#discount").attr('due_discount', '')

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
        const MAX_WIDTH = 360;
        const MAX_HEIGHT = 640;
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
            $pt = {{ $payType }}
            if($pt == 20 ){
                $("#client_pay").val(0)
            }
            if(parseInt($('#productsTable tbody tr').length) < 1){
                e.preventDefault();
                alert("أضف منتج أولا ‘ إلي الفاتورة قبل الحفظ!!")
                return ;
            }
            

            client_pay = ($("#client_pay").val())? parseFloat($("#client_pay").val()) : 0;
            
            payTaype_s = {{ $payType }}
            totalInv = parseFloat(totaleInoice).toFixed(2)
            console.log(totalInv, client_pay)

            // if( totalInv < client_pay){
            //     e.preventDefault();
            //     $("#client_pay").val("")
            //     alert("عذرا لا يمكن للعيل أن يدفع أكثر من مديونية الفاتورة" +  totalInv)
            //     return;
            // }

            $("#form-actionSubmit").html('<p>جاري الحفظ ... الرجاء الانتظار ...</p>')
            var canvas= document.getElementById('canvasId')
            var dataURL = canvas.toDataURL()
            // alert (dataURL.length)
            $("#imgData").val(dataURL)

            
        })

        // End of Compress image locallay befor uploadeng


    })
</script>

<script src="{{ asset('js/resources/dashboard/invoices/invoices_create.js?v=' .rand(1,100000)) }}"></script>
@endsection


