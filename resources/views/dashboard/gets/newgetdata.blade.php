<style>
    #getTable{
        width: 100%
    }
    #getTable td,
    #getTable th,
    #getTable p{
        padding: 0;
        margin: 0;
        font-size: 12px;
        text-align: center
    }
    #getTable thead th:first-child,
    #getTable tfoot th:first-child,
    #getTable tbody th{
        font-size: 10px;
        text-align: right;
        padding-right: 5px;
    }

    #getTable tbody th a{
        color: blue;
    }

    #getTable input{
        margin-top:5px; 
        width: 60px;
        display: block;
        margin: auto;
    }
    #getTable tr td:nth-child(5){
        background-color: red;
    }
    .formInputs div.dropdown-menu.show {
        left: auto !important;
        right: 0 !important;
    }

    #client_id, .filter-option-inner-inner, #client_id span.text, .dropdown-item{
        text-align: right !important;
        direction: rtl;
    }
    #is_client_payDiv label{
        font-size: 16px;
        color: #000;
    }

    .loadingImg{
        display: block;
        margin: 30px auto;

    }

    #divPay{
        display: flex;
        flex-direction: column;
        column-gap: 20px;
    }

    @media (min-width: 767px){
        #getTable td,
        #getTable th,
        #getTable p{
            padding: 5;
            font-size: 16px;
        }

        #getTable thead th:first-child,
        #getTable tfoot th:first-child,
        #getTable tbody th{
            font-size: 16px;
            padding-right: 5px;
        }
        #getTable input{
            margin-top:5px; 
            width: 100px;
            display: block;
            margin: auto;
        }

        #getTable tr:hover{
            background-color: #ccc;
        }
    }

    table#getTable i.delEdit{
        color:blue !important;
        cursor: pointer;
    }
</style>

{{-- @if(Auth::user()->can(['change_invoice_status']))
    <div style="display: flex; margin-bottom: 20px; clear: both; width: 100%;"><a style="color:blue;" target="_blank" href="{{ route('gets.zeroWalit', ['client'=>$client->id]) }}">{{ $client->client_name }}</a></div>
@endif --}}

<p>صافي مديونية العميل: <span>{{ $next_pro_total -  $walit }}</span></p>
<br>
<table id="getTable" border="1">
    <thead>
        <th>الصنف <br> السعر</th>
        <th style="text-align: center"><i class="fas fa-eye delEdit" style="font-size: 20px; color: #fff;" aria-hidden="true"></i></th>
        <th>خصم</th>
        <th>ك أجل</th>
        <th>ك سداد</th>
        <th>ك مرتجع</th>
        <th>قيمة</th>
        <th>باقي</th>
    </thead>

@foreach ($inv_pro as $pro)
    <tbody>
        <tr>
            <th><a href="{{ route('invoices.show', $pro['invoice_id']) }}">{{ $pro['Product_Name'] }}</a> <span style="display: block">{{ $pro['invoice_date'] }}</span></th>
            <td style="text-align: center"><i data-invid="{{ $pro['invoice_id'] }}" class="showInv fas fa-eye delEdit" style="font-size: 20px; color: #fff;" aria-hidden="true"></i></td>
            <td>{{ $pro['discount'] }}</td>
            <td>{{ $pro['get_quantity_next'] }}</td>
            <td><input min="0" max="{{ $pro['get_quantity_next'] }}" id="get-pro{{ $pro['invoice_product_id'] }}" data-invproid="{{ $pro['invoice_product_id'] }}" data-discount="{{ $pro['discount'] }}" data-type="get" data-invid="{{ $pro['invoice_id'] }}" data-proid="{{ $pro['product_id'] }}" data-runid="{{ $pro['runID'] }}" type="number" placeholder="سداد" ></td>
            <td><input min="0" max="{{ $pro['get_quantity_next'] }}" id="return-pro{{ $pro['invoice_product_id'] }}" data-invproid="{{ $pro['invoice_product_id'] }}" data-discount="{{ $pro['discount'] }}" data-type="return" data-invid="{{ $pro['invoice_id'] }}" data-proid="{{ $pro['product_id'] }}" data-runid="{{ $pro['runID'] }}" type="number" placeholder="مرتجع" ></td>
            <td><p id="val-pro{{ $pro['invoice_product_id'] }}" data-type="return" data-invid="{{ $pro['invoice_id'] }}" data-proid="{{ $pro['product_id'] }}" data-runid="{{ $pro['runID'] }}"></p></td>
            <td><p id="next-pro{{ $pro['invoice_product_id'] }}" data-type="return" data-invid="{{ $pro['invoice_id'] }}" data-proid="{{ $pro['product_id'] }}" data-runid="{{ $pro['runID'] }}">{{ $pro['get_pay_next'] }}</p></td>
        </tr>
    </tbody>
@endforeach
    <tfoot>
        
        <th style="text-align: center; font-weight: bold" colspan="6">الأجــمــالــــــــــــــــــــــي</th>
        <th style="background: yellow"><p id="val-pro-total"></p></th>
        <th style="background: red"><p id="next-pro-total">{{ $next_pro_total }}</p></th>
    </tfoot>
</table>

<div id="divPay">
    <div>
        <p>محفظة العميل: <span id="clientWalit">{{ $walit }}</span></p>
    </div>
    
    <div id="is_client_payDiv" style="margin: 10px 0px; display:none">
        {!! checkbox(['errors'=>$errors, 'name'=>'is_client_pay', 'check'=>false,
        'transval'=>'دفعة نقدية يتم توزيعها علي مديونية المنتجات والباقي يضاف إلي محفظة العميل', 'cols'=>12, 'class'=>'paymoney']) !!}
    </div>
    <div id="div_client_pay" style="display: none">
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'client_pay', 'transval'=>'دفعة نقدية', 'required'=>'required', 'cols'=>12, 'attr'=>'min="0" max=$next_pro_total step="0.01"']) !!}
    </div>
    <div>ما  تم إضافته لمحفظة العميل: <span id="clientWalitadd">{{ $walit }}</span></div>

    <div id="errorMsg"></div>
    <div id="submitData">
        {!! buttonAction('Save', '', 'saveSubmit', false) !!}
    </div>
</div>

