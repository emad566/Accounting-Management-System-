<form method="POST" action="{{ route('returns.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    <input type="hidden" name="invoice_id", value="{{ $invoice->id }}">

    <div class="row">
        {!! printData(['label'=>'العميل', 'data'=>$invoice->client->client_name, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
        {!! printData(['label'=>'من مخزن', 'data'=>$invoice->voucher->store->Store_Name, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
        {{-- {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'return_code', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'cols'=>3]) !!} --}}
        <?php
        $stores = '';
        $select_id = $invoice->voucher->store->id;
        if(Auth::user()->voucher_id){
            $select_id = Auth::user()->voucher->store->id;
            $stores = Auth::user()->stores->where('id', $select_id);
        }else{
            $stores = Auth::user()->stores->where('is_active', 1);
        }
        ?>
        {!! select(['errors'=>$errors, 'name'=>'to_store_id', 'frkName'=>'Store_Name', 'rows'=>$stores, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3, 'select_id'=> $select_id]) !!}

        {!! input(['errors'=>$errors, 'name'=>'return_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'تاريخ عمل المرتجع', 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
        


    </div>
    <div class="row">
        {!! printData(['label'=>'ملاحظات', 'data'=>$invoice->invoice_details, 'cols'=>12, 'id'=>'', 'class'=>'', ]) !!}
    </div>

    <div class="row">
        <div class="col-12 table-responsive">
            <table id="productsTable" class="mobileTable table-hover table-bordered color-bordered-table purple-bordered-table">
                <thead>
                    <th>#id</th>
                    <th>الصنف</th>
                    <th>{{ trans('validation.attributes.runID') }}</th>
                    <th>كمية المتاح</th>
                    <th>كمية المرتجع</th>
                    <th>كمية متاح البونص</th>
                    <th>كمية مرتجع االبونص</th>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    @foreach ($invoice->view_invoice_products as $product)
                        <?php
                            if(old('runIDs')){
                                $id_runID = $product->id . "_" . old('runIDs')[$i];
                            }else{
                                $id_runID = $product->id . "_" .$product->runIDs;
                            }
                        ?>

                        <tr id="row{{ $id_runID }}">
                            <td>{{ ++$i }}</td>
                            <td> <input type="hidden" name="invoice_product_ids[]" value="{{ $product->id }}">{{ $product->product->Product_Name }}</td>
                            <td>{{ $product->runID }}</td>
                            <td>{{ $product->get_quantity_next }}</td>
                            <td> <input
                                invoice_product_id="{{ $product->id }}"
                                available_quantity="{{ $product->get_quantity_next }}"
                                available_bounce="{{ $product->invoice_bounce_net }}"

                                type="number" name="return_quantitys[]" value="@if(old('return_quantitys')){{ old('return_quantitys')[$i-1] }}@endif" class="form-control return_quantitys"></td>
                            <td>{{ $product->invoice_bounce_net }}</td>
                            <td> <input id="return_bounce_{{ $product->id }}"
                                invoice_product_id="{{ $product->id }}"
                                available_quantity="{{ $product->get_quantity_next }}"
                                available_bounce="{{ $product->invoice_bounce_net }}"
                                type="number" name="return_bounces[]" value="@if(old('return_bounces')){{ old('return_bounces')[$i-1] }}@endif" class="form-control return_bounces"></td>
                        <tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
