<style>
    .formInputs label{
        font-size: 16px;
        color: #000;
    }

    #saveSubmit{
        display: none;
    }
</style>
<div id="client_idrow" class="row client_idrow">
</div>

<div class="formInputs row">
    {!! select_client(['errors' => $errors, 'name' => 'client_id', 'frkName' => 'client_name', 'rows' => $clients, 'transval' => 'اختر العميل', 'label' => true, 'required' => 'required', 'cols' => 4, 'attr' => 'data-live-search="true"']) !!}
    <?php
        $stores = '';
        $select_id = '';
        if(Auth::user()->voucher_id){
            $select_id = Auth::user()->voucher->store->id;
        }else if(Auth::user()->stores->count() == 1){
                $select_id = Auth::user()->stores->where('is_active', 1)->first()->id;
        }
        
        $stores = Auth::user()->stores->where('is_active', 1);

        ?>
        {!! select(['errors'=>$errors, 'select_id'=>$select_id, 'name'=>'to_store_id', 'frkName'=>'Store_Name', 'rows'=>$stores, 'transval'=>'اختر المخزن الذي يتم عمل المرتجع عليه', 'label'=>true, 'required'=>'required', 'cols'=>4, 'select_id'=> $select_id]) !!}

        {!! input(['errors'=>$errors, 'name'=>'insert_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'تاريخ التحصيل/المرتجع', 'label'=>true, 'required'=>'required', 'cols'=>4]) !!}
</div>
<div id="clientdata" class="row clientdata" style="border-bottom: dashed 2px blue; margin-top: 20px">
    
</div>

<div id="showInvDat"></div>



