@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">فاتورة إرجاع للمورد</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('outpermits.index') }}" class="btn btn-primary float-right">كل فواتير الإرتجاع</a>
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
                    <h6>بحث عن فاتوره  شراء</h6>
                    <hr>
                    <form method="POST" action="{{ route('outpermits.find_post') }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <div class="row">
                            {!! input(['errors'=>$errors, 'name'=>'inpermit_code', 'type'=>'number', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>12]) !!}
                        </div>

                        <div class="row">
                            {!! buttonAction('search') !!}
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
<script>
    $rowNum = 0;
    $('document').ready(function(){
        $(document).on("click", "#outpermitAdd", function(e){
            if(!$("#Buy_Price").val() || !$("#runID").val() || !$("#expire_date").val() || !$("#product_id").val() || !$("#Quantity").val() || $("#Quantity").val() <1){
                e.preventDefault();
                alert("من فضلك اختار صنف وكمية موجبة أقل من 9999999..! وباقي بيانات الصنف بششكل صحيح.")
                return ;
            }

            $uniqe = $("#product_id").val() + $("#runID").val()

            if($("#row"+$uniqe).length){
                e.preventDefault();
                alert(" هذا الصنف موجود بالفعل في اذن الإضافة يمكنك تغيير كميته علي أن تضيفه مرة أخري.!")
                return ;
            }

            $newRow = '<tr id="row'+ $uniqe +'">'
                        + '<td>'+ ++$rowNum + '</td>'
                        + '<td> <input type="hidden" name="product_ids[]" value="'+ $("#product_id").val() +'">' + $("#product_id option:selected").text() + '</td>'
                        + '<td> <input type="number" name="quantities[]" value="'+ $("#Quantity").val() +'" class="form-control" min="0" max="99999999"></td>'
                        + '<td> <input type="number" name="Buy_Prices[]" value="'+ $("#Buy_Price").val() +'" class="form-control" min="0" max="99999999" step="0.01"></td>'
                        + '<td> <input type="text" name="runIDs[]" value="'+ $("#runID").val() +'" class="form-control" min="0" max="99999999"></td>'
                        + '<td> <input type="date" name="expire_dates[]" value="'+ $("#expire_date").val() +'" class="form-control"></td>'
                        + '<td> <a href="#" delId="row'+ $("#product_id").val() +'" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>'
                     +'<tr>';

            $("#Quantity").val('')

            $("#product_id").val('')
            $("#runID").val('')
            $("#expire_date").val('')
            $("#Buy_Price").val('')
            tableBody = $("#productsTable tbody");
            tableBody.append($newRow);

        });

        $(document).on("click", ".prodcutDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            e.preventDefault();
        });
    })
</script>
@endsection


