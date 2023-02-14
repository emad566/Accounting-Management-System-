@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تحصيل فاتورة عميل  رقم: <a href="{{ route("invoices.show", $invoice->id) }}"><span class="badge badge-pill badge-cyan ml-auto">{{ $invoice->invoice_code }}</span></a></h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('vouchers.show', $invoice->voucher->id) }}" class="btn btn-primary float-right">إذن صرف رقم: {{ $invoice->voucher->voucher_code }} </a>
                @if(Auth::user()->can('Accounting'))
                <a href="{{ route('clients.accounting', $invoice->client_id) }}" class="btn btn-primary mx-2  float-right">عرض كشف حساب العميل: {{ $invoice->client->client_name }}</a>
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
                    <h6>تحصيل فاتورة عميل  رقم: <a href="{{ route("invoices.show", $invoice->id) }}"><span class="badge badge-pill badge-cyan ml-auto">{{ $invoice->invoice_code }}</span></a> </h6>
                    <hr>
                    @php $cols = 3; @endphp
                    @include('dashboard.gets.createModel')
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
        var client_balance = {{ $invoice->client->view_client->get_overPrice_sum }}
        $("#loginform").submit(function(e){
            client_pay = parseFloat($("#client_pay").val()).toFixed(2);
            payTaype_s = {{ $payType }}
            @if(!$generalpolicy->pay_more_invoice_balance)
            if( payTaype_s == 40 || payTaype_s == 10){
                totalInv = {{ $invoice->view_invoice->get_nexts }}
                maxPay = (client_balance >=0) ? totalInv : totalInv - client_balance;
                maxPay = (parseInt(maxPay/5) == maxPay/5)? maxPay : 5*(parseInt(maxPay/5)) +5
                if( maxPay < client_pay){
                    e.preventDefault();
                    $("#client_pay").val("")
                    alert("عذرا لا يمكن للعيل أن يدفع أكثر من " +  maxPay)
                    return;
                }
            }else{
                totaleInvoice = parseFloat($("#totaleInvoice").text()).toFixed(2)
                
                maxPay = (client_balance >=0) ? totaleInvoice : totaleInvoice - client_balance;
                maxPay = (parseInt(maxPay/5) == maxPay/5)? maxPay : 5*(parseInt(maxPay/5)) +5
                if(maxPay < client_pay){ 
                    e.preventDefault();
                    $("#client_pay").val("")
                    alert("عذرا لا يمكن للعيل أن يدفع أكثر من " +  maxPay )
                    return;
                }
            }
            @endif
            $("#form-actionSubmit").html('<p>جاري الحفظ ... الرجاء الانتظار ...</p>')  
        });

        $(document).on("change", "#pay_types", function(e){
            $url = "{{ url('dashboard/gets/' . $invoice->id . "/create") }}" + "/" + $("#pay_types").val()
            window.location.replace($url);
        });

        function setPaidPrices(){
            $(".pay_quantitys").each(function(){
                invoice_product_id = parseInt($(this).attr('invoice_product_id'))
                pay_quantity = parseInt($(this).val())
                public_price = parseFloat($(this).attr('public_price'))
                get_quantity = parseInt($(this).attr('get_quantity'))
                discount = parseFloat($(this).attr('discount'))

                if(pay_quantity>get_quantity|| pay_quantity<1){
                    $(this).val('')
                    $(this).focus()
                    pay_quantity = 0
                }

                client_balance = {{ $invoice->client->view_client->get_overPrice_sum }}

                paid_price = public_price*(100-discount) * pay_quantity / 100

                $("#paid_price_"+invoice_product_id).val(parseFloat(paid_price).toFixed(2))
                setTotalPaid()

            })
        }

        function setTotalPaid(){
            totaleInvoice = 0
            totalNexts = 0
            client_balance = {{ $invoice->client->view_client->get_overPrice_sum }}

            $(".paid_prices").each(function(){
                paid =  parseFloat($(this).val())
                totaleInvoice += (paid>0)? parseFloat(paid) : 0;
            })

            $(".paid_nexts").each(function(){
                paid =  parseFloat($(this).val())
                totalNexts += (paid>0)? parseFloat(paid) : 0;

            })

            if (totaleInvoice<0) {
                totaleInvoice = 0
            }

            $("#totalNext").text(parseFloat(totalNexts).toFixed(2))

            $("#totaleInvoice").text(parseFloat(totaleInvoice).toFixed(2))
            totalPaid = parseFloat(totaleInvoice) - parseFloat(client_balance)
            $("#totalPaid").text(parseFloat(totalPaid).toFixed(2))
            $("#client_pay").val()

            minPay = 5 * parseInt(totaleInvoice/5)
            $("#client_pay").attr("min", minPay)

            return totaleInvoice
        }

        @if(old('pay_quantitys'))
        setPaidPrices()
        @endif


        $(document).on("change", ".pay_quantitys", function(e){
            invoice_product_id = parseInt($(this).attr('invoice_product_id'))
            pay_quantity = parseInt($(this).val())
            public_price = parseFloat($(this).attr('public_price'))
            get_quantity = parseInt($(this).attr('get_quantity'))
            discount = parseFloat($(this).attr('discount'))

            if(pay_quantity>get_quantity|| pay_quantity<1){
                alert("عذرا: الكمية يجب ان تكون اكبر من الصفر واقل من او تساوي كمية الاجل.")
                $(this).val('')
                $(this).focus()
                pay_quantity = 0
            }

            client_balance = {{ $invoice->client->view_client->get_overPrice_sum }}

            paid_price = public_price*(100-discount) * pay_quantity / 100
            next_price = public_price*(100-discount) * (get_quantity-pay_quantity) / 100
            $("#paid_price_"+invoice_product_id).val(parseFloat(paid_price).toFixed(2))
            $("#paid_next_"+invoice_product_id).val(parseFloat(next_price).toFixed(2))

            setTotalPaid()
        });

        $(document).on("change", "#client_pay", function(e){
            $pay_type = $('#pay_types').val()
            if($pay_type == 40){
                $("#client_pay").attr('min', '0')
                client_balance = parseFloat($('#client_balance').text())
                client_pay = parseFloat($(this).val()) + parseFloat(client_balance)
                totalNext = 0
                totaleInvoice = 0
                $(".productItem").each(function () {
                    Public_Price = parseFloat($(this).find(".pay_quantityItem").attr('public_price'))
                    discount = parseFloat($(this).find(".pay_quantityItem").attr('discount'))
                    invoice_quantity = parseInt($(this).find(".pay_quantityItem").attr('get_quantity'))
                    unit_price = (100-discount)/100*Public_Price
                    capPayFloat = client_pay/unit_price

                    canPay_q = parseInt(client_pay/unit_price)

                    canPayPlus = canPay_q +1
                    canPayPlusPrice = unit_price * canPayPlus
                    canPay_q = (canPayPlusPrice<= parseFloat(client_pay).toFixed(2))? canPayPlusPrice : canPay_q;

                    canPay_q = (canPay_q<=invoice_quantity)? canPay_q : invoice_quantity;
                    payItemPrice = canPay_q * unit_price
                    paid_nextSum = invoice_quantity * unit_price - payItemPrice
                    nextQ = invoice_quantity - canPay_q
                    nextPrice = invoice_quantity * unit_price - payItemPrice
                    totalNext += parseFloat(nextPrice)
                    totaleInvoice += parseFloat(payItemPrice)

                    $(this).find(".paid_nexts").val(parseFloat(nextPrice).toFixed(2))
                    if(parseInt(canPay_q) > 0){
                        $(this).find(".pay_quantityItem").val(canPay_q)
                        $(this).find('.paid_prices').val(parseFloat(payItemPrice).toFixed(2))
                    }else{
                        $(this).find(".pay_quantityItem").val('')
                        $(this).find('.paid_prices').val('')
                    }
                    client_pay -= payItemPrice
                })
                totalRequired = parseFloat($("#totalRequired").text())
                $("#totalNext").text(parseFloat(totalNext).toFixed(2))
                $("#totaleInvoice").text(parseFloat(totaleInvoice).toFixed(2))
                $("#client_balance_diff").text(parseFloat(client_pay).toFixed(2))
            }else{
                client_pay = parseFloat($("#client_pay").val())
                totalPaid = parseFloat($("#totalPaid").text())
                totaleInvoice_fromHtml = parseFloat($("#totaleInvoice").text()).toFixed(2)
                
                
                minPay = 5 * parseInt(totalPaid/5)

                client_balance_diff = (client_pay - totalPaid).toFixed(2)

                $("#client_pay").attr("min", minPay)
                
                if(minPay<=client_pay){
                    $("#client_balance_diff").text(client_balance_diff)
                }else{

                    alert("عذرا قيمة السداد يجب ان تكون اكبر من أو تساوي: " +  minPay)
                    $("#client_pay").val()
                    $("#client_pay").focus()
                }
            }

            
        });

        @if(!$generalpolicy->pay_more_invoice_balance)
        $(document).on("focusout", "#client_pay", function(e){
            client_pay = parseFloat($(this).val()).toFixed(2);
            payTaype_s = {{ $payType }}

            if( payTaype_s == 40 || payTaype_s == 10){
                totalInv = {{ $invoice->view_invoice->get_nexts }}
                maxPay = (client_balance >=0) ? totalInv : totalInv - client_balance;
                maxPay = (parseInt(maxPay/5) == maxPay/5)? maxPay : 5*(parseInt(maxPay/5)) +5
                if( maxPay < client_pay){
                    e.preventDefault();
                    $("#client_pay").val("")
                    alert("عذرا لا يمكن للعيل أن يدفع أكثر من " +  maxPay)
                    return;
                }
            }else{
                totaleInvoice = parseFloat($("#totaleInvoice").text()).toFixed(2)
                
                maxPay = (client_balance >=0) ? totaleInvoice : totaleInvoice - client_balance;
                
                maxPay = (parseInt(maxPay/5) == maxPay/5)? maxPay : 5*(parseInt(maxPay/5)) +5
                if(maxPay < client_pay){ 
                    e.preventDefault();
                    $("#client_pay").val("")
                    alert("عذرا لا يمكن للعيل أن يدفع أكثر من " +  maxPay )
                    return;
                }
            }
        });
        @endif

    })
</script>
@endsection


