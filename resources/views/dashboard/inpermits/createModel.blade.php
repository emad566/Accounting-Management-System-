<form method="POST" action="{{ route('inpermits.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    <div class="row">
        {!! select(['errors'=>$errors, 'name'=>'supplier_id', 'frkName'=>'Sup_Name', 'rows'=>$suppliers, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>4, 'attr'=>'data-live-search="true"' ]) !!}
        {!! input(['errors'=>$errors, 'name'=>'inpermit_code', 'type'=>'number', 'transAttr'=>true, 'label'=>true, 'cols'=>4]) !!}
        {!! input(['errors'=>$errors, 'name'=>'inpermit_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'تاريخ الفاتورة', 'label'=>true, 'required'=>'required', 'cols'=>4]) !!}
        {!! input(['errors'=>$errors, 'name'=>'inpermit_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>false, 'cols'=>12]) !!}
    </div>

    <div class="row relationElements">
        {!! select(['errors'=>$errors, 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attrs'=>['Public_Price'], 'attr'=>'data-live-search="true"' ]) !!}
        {{-- {!! select(['errors' => $errors, 'name' => 'runID', 'frkName' => 'Public_Price', 'rows' => [], 'transval' => 'رقم التشغيلة', 'label' => true, 'cols' => 1, 'attr' => 'data-live-search="true"']) !!} --}}

       
        {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'runID', 'transAttr'=>true, 'label'=>true, 'cols'=>1, 'attr'=>'maxlength="50" minlength="1" list="runids"']) !!}
        <datalist id="runids">
            
        </datalist>
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'Public_Price', 'transAttr'=>true, 'class'=>'', 'label'=>true, 'cols'=>1, 'attr'=>'min="0" max="10000" step="0.01"']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'Buy_Price', 'transAttr'=>true, 'class'=>'isDisabled', 'label'=>true, 'cols'=>1, 'attr'=>'min="0" max="100" step="0.01" ']) !!}
        {!! input(['errors'=>$errors, 'type'=>'date', 'name'=>'create_date', 'transAttr'=>true, 'class'=>'isDisabled', 'label'=>true, 'attr'=>'', 'cols'=>2]) !!}
        {!! input(['errors'=>$errors, 'type'=>'date', 'name'=>'expire_date', 'transAttr'=>true, 'class'=>'isDisabled', 'label'=>true, 'attr'=>'', 'cols'=>2]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'Quantity', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'min="0" max="9999999"']) !!}

        <button type="button" id="inpermitAdd" class="btn btn-small btn-succes my-1 pull-left"><i class="fa fa-plus-circle"></i> أضف</button>
    </div>


    <div class="row">
        <div class="col-12 table-responsive">
            <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                <thead>
                    <th>#</th>
                    <th>الصنف</th>
                    <th>{{ trans('validation.attributes.runID') }}</th>
                    <th>{{ trans('validation.attributes.create_date') }}</th>
                    <th>{{ trans('validation.attributes.expire_date') }}</th>
                    <th>{{ trans('validation.attributes.Public_Price') }}</th>
                    <th>{{ trans('validation.attributes.Buy_Price') }}</th>
                    <th>الكمية</th>
                    <th>القيمة</th>
                    <th>حذف</th>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    @if (old('product_ids'))
                        @foreach (old('product_ids') as $product_id)
                            <?php $product = App\Models\Product::find($product_id); ?>
                            @if($product)
                                <?php $id_runID = $product_id . "_" . old('runIDs')[$i]; ?>
                                <tr id="row{{ $id_runID }}">
                                    <td data-th="#">{{ ++$i }}</td>
                                    <td data-th="الصنف"> <input type="hidden" name="product_ids[]" value="{{ $product_id }}">{{ $product->Product_Name }}</td>
                                    <td data-th="{{ trans('validation.attributes.runID') }}"> <input type="text" name="runIDs[]" value="{{ old('runIDs')[$i-1] }}" class="form-control"></td>
                                    <td data-th="{{ trans('validation.attributes.create_date') }}"> <input type="date" name="create_dates[]" value="{{ old('create_dates')[$i-1] }}" class="form-control"></td>
                                    <td data-th="{{ trans('validation.attributes.expire_date') }}"> <input type="date" name="expire_dates[]" value="{{ old('expire_dates')[$i-1] }}" class="form-control"></td>
                                    <td data-th="{{ trans('validation.attributes.Public_Price') }}"> <input type="number" name="Public_Prices[]" value="{{ old('Public_Prices')[$i-1] }}" class="form-control" min="0" max="10000" step="0.01"></td>
                                    <td data-th="{{ trans('validation.attributes.Buy_Price') }}"> <input type="number" name="Buy_Prices[]" value="{{ old('Buy_Prices')[$i-1] }}" class="form-control" min="0" max="100" step="0.01"></td>
                                    <td data-th="الكمية"> <input type="number" Public_Price="{{ $product->Public_Price }}" name="quantities[]" value="{{ old('quantities')[$i-1] }}" class="form-control"></td>
                                    <td data-th="القيمة"> <input type="number" name="prices[]" value="{{ old('prices')[$i-1] }}" class="form-control prices"></td>
                                    <td data-th="حذف"> <a href="#" delId="row{{ $id_runID }}" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>
                                <tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr id="totalTr">
                        <td data-th="الأجمالي" colspan="8">الأجمـــــالـــي</td>
                        <td data-th="الأجمالي" id="priceTotalTd" class="badge badge-warning"><input id="totalPrice" type="number" name="totalPrice" value="{{ old('totalPrice') }}" class="form-control totalPrice"  readonly></td>
                        <td></td>
                    </tr>
                <tfoot>
            </table>
            <input type="hidden" value="{{ $i }}" id="lastCount">
        </div>
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
