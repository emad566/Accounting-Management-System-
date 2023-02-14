<form method="POST" action="{{ route('vouchers.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    <div class="row">
        <?php $select_id = ($stores && $stores->count() == 1 )? $stores->first()->id : "" ; ?>
        {!! select(['errors'=>$errors, 'name'=>'store_id', 'frkName'=>'Store_Name', 'rows'=>$stores, 'transAttr'=>true, 'label'=>true, 'select_id'=>$select_id, 'required'=>'required', 'cols'=>4]) !!}
        <!-- {!! input(['errors'=>$errors, 'name'=>'voucher_code', 'type'=>'text', 'transAttr'=>true, 'label'=>true, 'cols'=>6]) !!} -->
        {!! input(['errors'=>$errors, 'name'=>'voucher_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>4]) !!}
        {!! input(['errors'=>$errors, 'name'=>'voucher_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>false, 'cols'=>12]) !!}
        @if(Auth::user()->can(['Voucher_all_store']))
        {!! checkbox(['errors'=>$errors, 'check'=>false, 'name'=>'voucherall', 'transval'=>'تحويل المخزن بالكامل كأذن صرف', 'cols'=>12]) !!}
        @endif
    </div>

    <div class="row relationElements">
        <div class="col-md-3 product_id">
            <div class="form-group"> <label for="runID">الصنف</label>
                <p>من فضلك إختر مخزن أولا</p>
            </div>
        </div>
        
        {!! select(['errors'=>$errors, 'name'=>'runID', 'frkName'=>'runID', 'rows'=>[], 'transAttr'=>true, 'label'=>true, 'cols'=>2]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'avliable', 'transval'=>"المتاح", 'label'=>true, 'cols'=>2, 'attr'=>'readonly']) !!}

        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'voucher_quantity_out', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'min="0" max="9999999"']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'rest', 'transval'=>"الباقي", 'label'=>true, 'cols'=>2, 'attr'=>'readonly']) !!}
        <button type="button" id="inpermitAdd" class="btn btn-small btn-success my-1"><i class="fa fa-plus-circle"></i> أضف</button>
    </div>

    
    <p>Execution Time: <span id="t1">0.0</span> Secs</p>


    <div class="row">
        <div class="col-12 table-responsive">
            <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                <thead>
                    <th>#</th>
                    <th>الصنف</th>
                    <th>{{ trans('validation.attributes.runID') }}</th>
                    <th>الكمية</th>
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
                                    <td>{{ ++$i }}</td>
                                    <td> <input type="hidden" name="product_ids[]" value="{{ $product_id }}">{{ $product->Product_Name }}</td>
                                    <td> <input type="text" name="runIDs[]" value="{{ old('runIDs')[$i-1] }}" class="form-control" readonly></td>
                                    <td> <input type="number" name="voucher_quantity_outs[]" value="{{ old('voucher_quantity_outs')[$i-1] }}" class="form-control" readonly></td>
                                    <td> <a href="#" delId="row{{ $id_runID }}" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>
                                <tr>
                            @endif
                        @endforeach

                    @endif
                </tbody>
            </table>
            <input type="hidden" value="{{ $i }}" id="lastCount">
        </div>
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
