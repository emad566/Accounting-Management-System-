@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'عمليات المندوب'])

@section('content')
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">عمليات المندوب</h4>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- /End Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div id="userProcesses" class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="searchForm" class="searchForm">
                        <div class="row">
                            <?php $selected_id = ($users->count()==1)? $users->first()->id : '' ; ?>
                            {!! select(['select_id'=>$selected_id, 'errors'=>'', 'name'=>'id', 'frkName'=>'fullName', 'rows'=>$users, 'transval'=>'العضو/المندوب', 'label'=>true, 'cols'=>3, 'attr'=>'data-live-search="true"']) !!}
                            {!! select(['errors'=>'', 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transval'=>'الصنف (فواتير فقط)', 'label'=>true, 'cols'=>3, 'attr'=>'data-live-search="true"']) !!}
                            {!! input(['errors'=>$errors, 'name'=>'created_at_from', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'من', 'label'=>true, 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'name'=>'created_at_to', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'إلي', 'label'=>true, 'cols'=>3]) !!}
                        </div>
                    </div>
                    <div class="row">
                        {!! checkbox(['errors'=>$errors, 'check'=>false, 'name'=>'is_sys_date', 'transval'=>'البحث بتاريخ السيستم', 'cols'=>3]) !!}
                        {!! checkbox(['errors'=>$errors, 'check'=>true, 'name'=>'is_transfer', 'transval'=>'عرض التحويلات المخزنية', 'cols'=>3]) !!}
                        {!! checkbox(['errors'=>$errors, 'check'=>true, 'name'=>'is_invoices', 'transval'=>'عرض الفواتير', 'cols'=>3]) !!}
                        {!! checkbox(['errors'=>$errors, 'check'=>true, 'name'=>'is_returns', 'transval'=>'عرض المرتجعات', 'cols'=>3]) !!}
                        {!! checkbox(['errors'=>$errors, 'check'=>true, 'name'=>'is_gets', 'transval'=>'عرض التحصيلات', 'cols'=>3]) !!}
                        {!! checkbox(['errors'=>$errors, 'check'=>true, 'name'=>'is_spends', 'transval'=>'عرض المصروفات', 'cols'=>3]) !!}
                        {!! checkbox(['errors'=>$errors, 'check'=>true, 'name'=>'is_transactions', 'transval'=>'عرض التحويلات المالية', 'cols'=>3]) !!}
                        {!! checkbox(['errors'=>$errors, 'check'=>true, 'name'=>'is_vouchers', 'transval'=>'عرض أذونات الصرف', 'cols'=>3]) !!}
                        {!! buttonAction('', 'بحث <i class="fab fa-searchengin"></i>', 'searchbtn', false) !!}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row" id="sm-response">
        
    </div>
</div>
@endsection

@section('script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

<script>
    $('document').ready(function(){
        var imgloading = ' <div class="loadingImg">جاري التحميل ... الرجاء الانتظار ...<img class="loadingImg" src="{{ asset('images/Loading.gif') }}" alt="loading"></div>';

        function getResponse(){
            document.getElementById('sm-response').innerHTML = imgloading;
            var formData = {
                user_id:document.getElementById('id').value,
                product_id:document.getElementById('product_id').value,
                created_at_from:document.getElementById('created_at_from').value,
                created_at_to:document.getElementById('created_at_to').value,
                is_sys_date:(document.querySelector('.is_sys_date').checked),

                is_transfer:(document.querySelector('.is_transfer').checked),
                is_invoices:(document.querySelector('.is_invoices').checked),
                is_returns:(document.querySelector('.is_returns').checked),
                is_gets:(document.querySelector('.is_gets').checked),
                is_spends:(document.querySelector('.is_spends').checked),
                is_transactions:(document.querySelector('.is_transactions').checked),
                is_vouchers:(document.querySelector('.is_vouchers').checked),
            }
            console.log(document.getElementById('id').value)
            $.ajax({
                url: '{{ route('reports.repprocessesresponse') }}',
                data:formData,
                type: "get",
                cache: false,
                success: function (response) {
                    document.getElementById('sm-response').innerHTML = response.data.strHTML;
                    console.log(response)

                    /* ===============================
                    ||  Start: Accept invoice
                    ================================== */
                    setTimeout(function(){
                        document.querySelectorAll('.accept').forEach(elm => {
                            elm.onclick = function (evt){

                                if(elm.href && elm.id != 'createInv'){
                                    evt.preventDefault()
                                    elm.textContent = 'جاري الحفظ'
            
                                    $.ajax({
                                        url: elm.href+"/1",
                                        type: "get",
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token()}}'
                                        },
                                        cache: false,
                            
                                        success: function (data) {
                                            elm.removeAttribute('href')
                                            elm.textContent = 'تم الموافقة بنجاح'
                                            elm.style.backgroundColor = "#fff"
                                            
                                            document.getElementById('collapse'+elm.dataset.invoiceid).classList.remove('show')
                                            document.getElementById('heading'+elm.dataset.invoiceid).classList.remove('notAccept')
                                            document.getElementById('heading'+elm.dataset.invoiceid).classList.remove('notAcceptFirst')
                                        },
                                        error: function (xhr) {
                                            alert("false")
                                            alert("Error: - " + xhr.status + " " + xhr.statusText);
                                        }
                                    });
                                }
                                
                            }
                        });
                    }, 1000)
                    /* End: Accept invoice */ 
                },
                error: function (xhr) {
                    alert("Error: - " + xhr.status + " " + xhr.statusText);
                }
            });
        }
        getResponse();

        $(document).on('submit', '#searchForm', function(e){
            e.preventDefault();
            getResponse()
        })
        $('select').selectpicker();

    });


</script>
<style>
    #userProcesses label, #userProcesses .printDataLable{
        color: black;
        font-weight: bold;
        font-size: 14px;
    }
</style>
@endsection

