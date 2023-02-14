@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل سياسات عميل</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('clientpolicys.index') }}" class="btn btn-primary float-right">كل سياسات العملاء</a>
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
                    <h6>تعديل سياسات العميل: {{ $client->client_Name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('clientpolicys.update', $client->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $client->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <?php
                            $readonly = (Auth::user()->can(['Accountant']) || Auth::user()->can(['SupperAdmin'])) ? '' : ' readonly="readonly" ' ;
                        ?>

                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$client, 'type'=>'text', 'name'=>'client_name', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'attr'=>$readonly, 'cols'=>3]) !!}
                            {!! select(['errors'=>$errors, 'edit'=>$client, 'name'=>'client_type_id', 'frkName'=>'name', 'rows'=>$client_types, 'transval'=>'النوع', 'label'=>true, 'required'=>'required', 'attr'=>$readonly, 'cols'=>3 ]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$client, 'type'=>'number', 'name'=>'client_due_limit', 'transAttr'=>true, 'cols'=>3, 'view'=>'اترك الحقل فارخ ليرث من سياسات المنطقة']) !!}
                            {!! select(['errors'=>$errors,'edit'=>$client, 'name'=>'is_multi_due_inherit_id', 'frkName'=>'name', 'rows'=>$isinherits, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
                        </div>



                        <div class="row">
                            {!! checkbox(['errors'=>$errors, 'edit'=>$client, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
                        </div>

                        <div class="row">
                            {!! buttonAction() !!}
                        </div>
                    </form>

                    <form method="GET" action="{{ route('clientpolicys.productpolicys') }}" id="productpolicysForm" class="form-horizontal form-material">
                        @csrf
                        <input type='hidden' name='_method' value='POST'>
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <hr><hr><hr><hr><hr>
                        <h4>سياسات أصناف العميل:</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mobileTable" id="productPolicysTable">
                                <thead>
                                    <tr>
                                        <th>الصنف</th>
                                        <th>الصنف</th>
                                        <th>أعلي خصم للنقدي</th>
                                        <th>أعلي خصم للأجل</th>
                                        <th>أكثر من أجل لنفس الصنف</th>
                                        <th>حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="pptbody">
                                @if($client->productpolicys && $client->productpolicys->count()>0)
                                <?php
                                    $productpolicys = $client->productpolicys->sortBy(function($q){
                                        return $q->product->Product_code;
                                    })
                                    ->all();
                                ?>
                                @foreach ($productpolicys as $productpolicy)
                                    <tr id="policy_{{ $productpolicy->id }}">
                                        <td>{{ $productpolicy->product->Product_Name }}</td>
                                        <td>{{ $productpolicy->product->Product_code }}</td>
                                        <td>{{ $productpolicy->paid_discount }}</td>
                                        <td>{{ $productpolicy->due_discount }}</td>
                                        <td>{{ $productpolicy->is_multi_due_inherit->name }}</td>
                                        <td><a policy_id="{{ $productpolicy->id }}" href="{{ route('clientpolicys.destroy', $productpolicy->id) }}" class="deletePolicy"><i class="fas fa-trash-alt delEdit"></i></a></td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>


                        <div class="row">
                            {!! select(['errors'=>$errors, 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'paid_discount', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="0" max="100" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من سياسات الصنف']) !!}
                            {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'due_discount', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="0" max="100" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من سياسات الصنف']) !!}
                            {!! select(['errors'=>$errors, 'name'=>'is_multi_due_inherit_id', 'frkName'=>'name', 'rows'=>$isinherits, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
                        </div>
                        <div class="row">
                            <p id="saveAlert" style="display: none">جاري الحفظ ...</p>
                            {!! buttonAction('', 'حفظ سياسة المنتج للعميل', 'savepolicy', false) !!}
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
    $(document).ready(function(){
        $(document).on('submit', '#productpolicysForm', function(e){
            e.preventDefault();
            $("#saveAlert").show()
            $.ajax({
                    url: '{!! route('clientpolicys.productpolicys') !!}',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token()}}'
                    },
                    cache: false,
                    data:{
                        client_id: {{ $client->id }},
                        product_id: $("#productpolicysForm #product_id").val(),
                        is_multi_due_inherit_id: $("#productpolicysForm #is_multi_due_inherit_id").val(),
                        paid_discount: $("#productpolicysForm #paid_discount").val(),
                        due_discount: $("#productpolicysForm #due_discount").val(),
                    },
                    success: function(data){
                            $("#pptbody").html(data)
                            $("#saveAlert").hide()
                        },
                    error: function(xhr){
                            alert(xhr.status+' '+xhr.statusText);
                            $("#saveAlert").hide()
                        }
            });

            return false;
        })

        $(document).on('click', '.deletePolicy', function(e){
            e.preventDefault();
            url_str= $(this).attr('href')
            policy_id = $(this).attr('policy_id')
            $(this).html("جاري الحذف ...")
            $.ajax({
                    url: url_str,
                    type: "GET",
                    success: function(data){
                            if(data){
                                // $("#policy_" + policy_id).remove();
                                $(document).find("#policy_" + policy_id).remove()
                                // $(this).closest('tr').remove();
                            }else{
                                alert("حدث خطأ أثناء الحذف")
                            }
                        },
                    error: function(xhr){
                            alert(xhr.status+' '+xhr.statusText);
                            $("#saveAlert").hide()
                        }
            });


        })
    });
</script>
@endsection

