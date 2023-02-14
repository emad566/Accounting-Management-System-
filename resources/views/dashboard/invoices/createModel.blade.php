<form method="POST" action="{{ route('invoices.store') }}" class="" id="loginform"  enctype="">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    <input type="hidden" name="voucher_id", value="{{ $voucher->id }}">

    <div class="row">
        <?php $select_id = (old('pay_types'))? old('pay_types'): $payType ;  ?>
        {!! input(['errors'=>$errors, 'edit'=>$voucher->store, 'name'=>'Store_Name', 'type'=>'text', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'disabled']) !!}
        {!! select(['errors'=>$errors, 'name'=>'pay_types', 'frkName'=>'name', 'rows'=>$pay_types, 'select_id'=>$select_id, 'transval'=>'نوع السداد', 'notrans'=>true, 'required'=>'required', 'label'=>true, 'cols'=>2]) !!}
        {!! select(['errors'=>$errors, 'name'=>'client_id', 'frkName'=>'client_name_title', 'rows'=>$clients, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}
        {!! input(['errors'=>$errors, 'name'=>'invoice_code', 'type'=>'text', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
        {!! input(['errors'=>$errors, 'name'=>'invoice_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
        {{-- {!! img(['errors'=>$errors, 'type'=>'text', 'name'=>'image', 'accept'=>'image/*', 'transval'=>'صورة الفاتورة', 'DragFileHer'=>'إضغط هنا لإختيار صورة الفاتورة', 'cols'=>12]) !!} --}}
        <p>صورة الفاتورة</p>
        <textarea class="hidden" name="imgData" id="imgData"></textarea>
        <input id="image" type="file" accept="image/*" style="display:block"/>
        <div id="previewInvoice" class="invoiceImage">
            <img id="previewinvoiceImg" src="">
        </div>
    </div>

    <div class="row">
        {!! input(['errors'=>$errors, 'name'=>'invoice_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>false, 'cols'=>12]) !!}
    </div>

    <div class="row relationElements">
        <div class="col-md-12"><p>سعر الجمهور: <span id="Public_Price"></span></p></div>
        {!! select(['errors'=>$errors, 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>2]) !!}
        {!! select(['errors'=>$errors, 'name'=>'runID', 'frkName'=>'runID', 'rows'=>[], 'transAttr'=>true, 'label'=>true, 'cols'=>1]) !!}
        {{-- {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'runID', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'min="0" max="9999999"']) !!} --}}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'avliable', 'transval'=>"المتاح", 'label'=>true, 'cols'=>1, 'attr'=>'readonly']) !!}

        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'invoice_quantity', 'transAttr'=>true, 'label'=>true, 'cols'=>1, 'attr'=>'min="0" max="9999999"', 'class'=>'inptutNull']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'bounce', 'transAttr'=>true, 'label'=>true, 'cols'=>1, 'class'=>'inptutNull']) !!}

        <?php

            $hidden = ($select_id != 30)? 'hidden' : '';
            $label = ($hidden)? false : true;
        ?>
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'pay_quantity', 'transval'=>"كمية السداد", 'label'=> $label, 'cols'=>1, 'class'=>'inptutNull '.$hidden, 'wrapperClass'=>$hidden]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'discount', 'transAttr'=>true, 'label'=>true, 'cols'=>1, 'attr'=>'min="0" max="100" step="0.01"', 'class'=>'inptutNull']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'pay_price', 'transval'=>"القيمة", 'label'=>true, 'cols'=>1, 'attr'=>'readonly', 'class'=>'inptutNull']) !!}


        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'paid_price', 'transval'=>"سداد", 'label'=> $label, 'cols'=>1, 'attr'=>'readonly', 'class'=>'inptutNull '.$hidden]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'paid_next', 'transval'=>"أجل", 'label'=> $label, 'cols'=>1,  'attr'=>'readonly', 'class'=>'inptutNull '.$hidden]) !!}
        <button type="button" id="inpermitAdd" class="btn btn-small btn-success my-1"><i class="fa fa-plus-circle"></i> أضف</button>
    </div>


    <div class="row">
        <div class="col-12 table-responsive">
            <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                <thead>
                    <th>#id</th>
                    <th>الصنف</th>
                    <th>{{ trans('validation.attributes.runID') }}</th>
                    <th>الكمية</th>
                    <th>بونص</th>
                    <th>خصم</th>
                    <th>القيمة</th>
                    <th>كمية السداد</th>
                    <th>سداد</th>
                    <th>أجل</th>
                    <th>حذف</th>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    @if (old('product_ids'))
                        @foreach (old('product_ids') as $product_id)
                            <?php $product = App\Models\Product::find($product_id); ?>
                            @if($product)
                                <?php $id_runID = $product_id . "_" . old('runIDs')[$i];
                                $Public_Price = Public_Price($product_id, old('runIDs')[$i]);
                                ?>
                                <tr id="row{{ $id_runID }}" class="productItem">
                                    <td>{{ ++$i }}</td>
                                    <td> <input type="hidden" name="product_ids[]" value="{{ $product_id }}">{{ $product->Product_Name }}</td>
                                    <td> <input type="text" name="runIDs[]" value="{{ old('runIDs')[$i-1] }}" class="form-control" readonly></td>
                                    <td> <input type="number" name="invoice_quantitys[]" value="{{ old('invoice_quantitys')[$i-1] }}" class="form-control" readonly></td>
                                    <td> <input type="number" name="bounces[]" value="{{ old('bounces')[$i-1] }}" class="form-control" readonly></td>
                                    <td> <input type="number" name="discounts[]" value="{{ old('discounts')[$i-1] }}" class="form-control" readonly></td>
                                    <td> <input type="number" name="pay_prices[]" value="{{ old('pay_prices')[$i-1] }}" class="form-control pay_priceSum" readonly></td>
                                    <td> <input type="number" name="pay_quantitys[]" value="{{ old('pay_quantitys')[$i-1] }}" class="form-control pay_quantityItem" readonly
                                        discount="{{ old('discounts')[$i-1] }}" invoice_quantity="{{ old('invoice_quantitys')[$i-1] }}" Public_Price="{{ $Public_Price }}"></td>
                                    <td> <input type="number" name="paid_prices[]" value="{{ old('paid_prices')[$i-1] }}" class="form-control paid_priceSum" readonly></td>
                                    <td> <input type="number" name="paid_nexts[]" value="{{ old('paid_nexts')[$i-1] }}" class="form-control paid_nextSum" readonly></td>
                                    <td> <a href="#" delId="row{{ $id_runID }}" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>
                                <tr>
                            @endif
                        @endforeach
                    @endif
                    <tr class="totalTr">
                        <th colspan="6">إجمــــالي</th>
                        <td data-th="القيمة"><span style="color: #000;" id="totalRequired">0</span></td>
                        <td></td>
                        <td data-th="سداد"><span style="color: blue;" id="totalPaid">0</span></td>
                        <td data-th="أجل"><span style="color: red;" id="totalNext">0</span></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <hr>
            {{-- <p>إجمالي الفاتورة: <span style="color: #000;" id="totalRequired">0</span> جم</p> --}}
            <p>محفظة العميل: <span id="client_balance">0</span></p>
            <p>المطلوب سدادة: <span style="color: blue;" id="totalPaid">0</span> جم</p>
            {{-- <p>إجمالي الآجل: <span style="color: red;" id="totalNext">0</span> جم</p> --}}
            <?php
            $v = ($payType == 20)? 0 : '';
            $isReadOnly = ($payType == 20)? 'readonly' : '';
            ?>

            <?php 
                $readonly = ($payType == 20)? 'readonly="readonly"' : '';
                $input_value = ($payType == 20)? 0 : '';
            ?>

            {!! input(['errors'=>$errors, 'name'=>'client_pay', 'value'=>$input_value, 'type'=>'number', 'transval'=>"سداد", 'label'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" step="0.01" ' .$readonly]) !!}
            <p>فرق حساب يضاف إلي محفظة العميل: <span id="client_balance_diff">0</span></p>
            <input type="hidden" value="{{ $i }}" id="lastCount">
        </div>
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
