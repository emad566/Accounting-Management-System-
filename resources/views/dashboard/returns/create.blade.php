@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">عمل مرتجع علي فاتورة عميل رقم: <a href="{{ route("invoices.show", $invoice->id) }}"><span class="badge badge-pill badge-cyan ml-auto">{{ $invoice->invoice_code }}</span></a></h4>
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
                    <h6 class="card-title">عمل مرتجع علي فاتورة عميل رقم: <a href="{{ route("invoices.show", $invoice->id) }}"><span class="badge badge-pill badge-cyan ml-auto">{{ $invoice->invoice_code }}</span></a> </h6>
                    <hr>
                    @php $cols = 3; @endphp
                    @include('dashboard.returns.createModel')
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
        $("#loginform").submit(function(){
            $("#form-actionSubmit").html('<p>جاري الحفظ ... الرجاء الانتظار ...</p>')
        });

        $(document).on("change", ".return_quantitys, .return_bounces", function(e){
            invoice_product_id = parseInt($(this).attr('invoice_product_id'))

            return_quantity = parseInt($(this).val());
            if(isNaN(return_quantity)) {return_quantity = 0}

            return_bounce = parseInt($("#return_bounce_"+invoice_product_id).val())
            if(isNaN(return_bounce)) {return_bounce = 0}

            available_quantity = parseInt($(this).attr('available_quantity'))
            available_bounce = parseInt($(this).attr('available_bounce'))

            if($(this).hasClass('return_quantitys')){
                if(return_quantity>available_quantity|| return_quantity<0){
                    alert("عذرا: كمية المرتجع يجب ان تكون اقل من او تساوي كمية المتاج.")
                    $(this).val('')
                    $(this).focus()
                }
            }

            if($(this).hasClass('return_bounces')){
                if(return_bounce>available_bounce || return_bounce<0){
                    alert("عذرا: كمية مرتجع البونص يجب ان تكون اقل من او تساوي كمية متاح البونص.")
                    $(this).val('')
                    $(this).focus()
                }
            }
        });

        $(document).on("click", "#save", function(e){
            total = 0;
            $(".return_quantitys").each(function(){
                invoice_product_id = parseInt($(this).attr('invoice_product_id'))

                return_quantity = parseInt($(this).val());
                if(isNaN(return_quantity)) {return_quantity = 0}
                return_bounce = parseInt($("#return_bounce_"+invoice_product_id).val())
                if(isNaN(return_bounce)) {return_bounce = 0}

                total += return_quantity + return_bounce
            });

            if(total<1){

                alert("عذرا لا يمكن انشاء فاتورة بدون كميات.")
                e.preventDefault()
                return;
            }
        });
    })
</script>
@endsection


