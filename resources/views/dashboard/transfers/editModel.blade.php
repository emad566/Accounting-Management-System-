<form method="POST" action="{{ route('transfers.update', $transfer->id) }}" class="form-horizontal form-material" id="loginform">
    @csrf
    <input type='hidden' name='_method' value='PUT'>

    <input type='hidden' name='transfer_id' value='{{ $transfer->id }}'>
    <input type='hidden' name='id' value='{{ $transfer->id }}'>

    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <h6>أمر تحويل: </h6>
    
    <div class="row">
        {!! select(['errors'=>$errors, 'edit'=>$transfer, 'name'=>'from_store_id', 'frkName'=>'Store_Name', 'rows'=>$stores_from, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3] ) !!}
        {!! select(['errors'=>$errors, 'edit'=>$transfer, 'name'=>'to_store_id', 'frkName'=>'Store_Name', 'rows'=>$stores_to, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3] ) !!}

        <!-- {!! input(['errors'=>$errors, 'edit'=>$transfer, 'name'=>'transfer_code', 'type'=>'text', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3] ) !!} -->
        {!! input(['errors'=>$errors, 'edit'=>$transfer, 'name'=>'transfer_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3] ) !!}
        {!! input(['errors'=>$errors, 'edit'=>$transfer, 'name'=>'transfer_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'cols'=>12] ) !!}
    </div>
    <hr>
    <div class="row relationElements">
        {!! select(['errors'=>$errors, 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>3]) !!}
        {!! select(['errors'=>$errors, 'name'=>'runID', 'frkName'=>'runID', 'rows'=>[], 'transAttr'=>true, 'label'=>true, 'cols'=>1]) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'avliable', 'transval'=>"المتاح", 'label'=>true, 'cols'=>2, 'attr'=>'readonly']) !!}

        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'Quantity', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'class'=>'inptutNull', 'attr'=>'min="0" max="9999999"']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'rest', 'transval'=>"الباقي", 'label'=>true, 'cols'=>2, 'class'=>'inptutNull', 'attr'=>'readonly']) !!}
        <button type="button" id="inpermitAdd" class="btn btn-small btn-success my-1"><i class="fa fa-plus-circle"></i> أضف</button>
        <button type="button" id="cancelEdit" class="btn btn-small btn-danger mx-1" style="display: none"><i class="fa fa-eject"></i>الغاء</button>

    </div>


    <hr>

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
                    <?php
                        $products = (old('product_ids'))? old('product_ids') : $transfer->products;
                    ?>
                    <?php $i=0; ?>
                    @foreach ($products as $product)
                        <?php
                            $product_id =  (old('product_ids'))? old('product_ids')[$i] : $product->id;
                            $quantity =  (old('quantities'))? old('quantities')[$i] : $product->pivot->Quantity;
                            $runID =  (old('runIDs'))? old('runIDs')[$i] : $product->pivot->RunID;
                            $Product_Name = App\Models\Product::findOrFail($product_id)->Product_Name;
                            $id_runID = $product_id . "_" . $runID;
                        ?>
                        @if($Product_Name)
                            <tr id="row{{ $id_runID }}">
                                <td>{{ ++$i }}</td>
                                <td> <input type="hidden" name="product_ids[]" value="{{ $product_id }}">{{ $Product_Name }}</td>
                                <td> <input type="text" name="runIDs[]" value="{{ $runID }}" class="form-control" readonly></td>
                                <td> <input type="number" name="quantities[]" value="{{ $quantity }}" class="form-control" readonly></td>
                                <td> <a href="#" delId="row{{ $id_runID }}" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a>
                                    <a href="#"

                                    product_id="{{ $product_id }}"
                                    runID="{{ $runID }}"
                                    quantity="{{ $quantity }}"

                                    delId="row{{ $id_runID }}" class="productEdit"><i class="fas fa-edit delEdit"></i></a> </td>
                                </td>
                            <tr>
                        @endif
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
