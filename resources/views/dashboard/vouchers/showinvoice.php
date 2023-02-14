<?php $isFirst = (!$invoice->client->is_first_add) ? true : false; ?>
<?php $count = $invoice->id; ?>
<div  class="col-12 @if($invoice->status->id != 20 && $isFirst) notAcceptFirst @elseif($invoice->status->id != 20 && !$isFirst) notAccept @endif my-1 card-header bg-info text-primary" id="heading{{ $invoice->id }}">
    <div class="row">
        <?php
        $flag = ($count==1)? "true": "false";
        // $show = ($count==1)? "show": "";
        $show = ($count==1)? "": "";
        ?>
        {!! printData(['label'=>'#', 'data'=>'<a href="'.route('invoices.show', $invoice->id).'">'.$invoice->invoice_code.'</a>', 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
        {!! printData(['label'=>'العميل', 'data'=>$invoice->client->client_name, 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
        {!! printData(['label'=>'التاريخ', 'data'=>$invoice->created_at . ' ('.$invoice->created_at->diffForHumans().') ', 'cols'=>4, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
        {!! printData(['label'=>'العميل دفع', 'data'=>$invoice->view_invoice->client_pay, 'cols'=>3, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
        <div class="col-md-1">
            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse{{ $invoice->id }}" aria-expanded="{{ $flag }}" aria-controls="collapse{{ $invoice->id }}">
                <i class="fas fa-eye delEdit" style="font-size: 20px; color: #fff;"></i> عرض
            </button>
        </div>
    </div>
</div>

<div id="collapse{{ $invoice->id }}" class="col-12 collapse {{ $show }}" aria-labelledby="heading{{ $invoice->id }}" data-parent="#accordion">

    <h6 class="card-title"></h6>
    <div class="row">
    @if($invoice->status->id <20 && $invoice->rep_user_id == Auth::id())
        <a href="{{ route('invoices.destroy', [$invoice->id]) }}" class="btn btn-danger float-right btn-danger mx-1">حذف</a>
    @endif

    @if(!( Auth::user()->voucher_id != $invoice->voucher_id
        || Auth::user()->voucher->voucher_status !=3
        || $invoice->status->id == 20))
    <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-warning float-right btn-info mx-1">تعديل</a>
    <a href="{{ route('invoices.destroy', $invoice->id) }}" class="btn btn-danger float-right btn-info mx-1">حذف</a>
    @endif

    @if(Auth::user()->can(['Create gets']) && $invoice->status->id ==20 && $invoice->view_invoice->get_nexts >0)
        <a href="{{ route('gets.create', $invoice->id) }}" class="btn btn-infor float-right btn-success mx-1">تحصيل</a>
    @endif
    @if(Auth::user()->can(['Create_return']) && $invoice->status->id ==20 && $invoice->view_invoice->get_nexts >0)
        <a href="{{ route('returns.create', $invoice->id) }}" class="btn btn-light float-right btn-light mx-1">عمل مرتجع</a>
    @endif
    </div>
    <hr>
    @php $cols = 3; @endphp


    <div class="row">
        {!! printData(['label'=>'العميل', 'data'=>$invoice->client->client_name, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
        {!! printData(['label'=>'نوع السداد', 'data'=>'', 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
        {!! printData(['label'=>'التاريخ', 'data'=>$invoice->invoice_date, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
    </div>
    <div class="row">
        {!! printData(['label'=>'ملاحظات', 'data'=>$invoice->invoice_details, 'cols'=>12, 'id'=>'', 'class'=>'', ]) !!}
    </div>
    <div class="showimage">
        <img class="invoiceImage" src="{{ url($invoice->image_rel_path()) }}" alt="">
    </div>
    <div class="col-12 table-responsive">
    <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
        <thead>
            <th>#id</th>
            <th>الصنف</th>
            <th>{{ trans('validation.attributes.runID') }}</th>
            <th>سعر الجمهور</th>
            <th>الكمية</th>
            <th>صافي الكمية</th>
            <th>بونص</th>
            <th>خصم</th>
            <th>القيمة</th>
            <th>كمية السداد</th>
            <th>سداد جم</th>
            <th>أجل جم</th>
        </thead>
        <tbody>
        <?php $i=1; ?>
        @foreach ($invoice->view_invoice_products as $ip)
        <tr>
            <td>{{ $i }}</td>
            <td>{{ $ip->product->Product_Name }}</td>
            <td>{{ $ip->runID }}</td>
            <td>{{ $ip->invoice_public_price }}</td>
            <td>{{ $ip->invoice_quantity }}</td>
            <td>{{ $ip->invoice_net_q_withoutbounce }}</td>
            <td>{{ $ip->invoice_bounce_net }}</td>
            <td>{{ $ip->discount }}</td>
            <td>{{ $ip->get_required }}</td>
            <td>{{ $ip->get_quantity }}</td>
            <td>{{ $ip->get_paid }}</td>
            <td>{{ $ip->get_next }}</td>
        </tr>
        <?php $i++; ?>
        @endforeach
        <tr class="totalTr">
            <th colspan="8">إجمــــالي</th>
            <td>{{ $invoice->view_invoice->get_requireds }}</td>
            <td></td>
            <td>{{ $invoice->view_invoice->get_paids }}</td>
            <td>{{ $invoice->view_invoice->get_nexts }}</td>
        </tr>
        </tbody>
    </table>
    </div>

    <p>العميل دفع: <span style="color: blue;" id="totalPaid">{{ $invoice->view_invoice->client_pay }}</span> جم</p>
    <p>تحصيل من محفظة العميل: <span style="color: blue;" id="totalPaid">{{ $invoice->view_invoice->paid_from_client_balance_sum }}</span> جم</p>
    <p>ما تم إضافته لمحفظة العميل: <span id="client_balance_diff">{{ $invoice->view_invoice->get_overPrice_sum }}</span> جم</p>

    <input type="hidden" value="{{ $i }}" id="lastCount">

    <div class="row"  @if (!$invoice->client->is_first_add) style="background:red; margin:30px; padding:20px;"  @endif>
        @if(Auth::user()->can(['change_invoice_status']) && $invoice->invoice_status_id==1)
        <a data-invoiceid="{{ $invoice->id }} class="requestEdit btn btn-warning" href="{{ route('invoices.changestatus', ['invoice'=>$invoice->id, 'status'=>10]) }}">طلب تعديل</a>
        @endif
        <?php
        if (!$invoice->client->is_first_add) {
            $accept_title = 'موافقة العميل وموافقة الفاتورة';
            $delete_title = 'حذف العميل وحذف الفاتورة';
        } else {
            $accept_title = 'موافقة';
            $delete_title = 'حذف';
        }
        ?>

        

        @if(Auth::user()->can(['change_invoice_status']) && $invoice->invoice_status_id!=20)
        <a aria-disabled="true" data-invoiceid="{{ $invoice->id }}" class="accept btn btn-success mx-1" href="{{ route('invoices.changestatus', ['invoice'=>$invoice->id, 'status'=>20]) }}">{{ $accept_title }}</a>
        @endif
        <p></p>
    </div>
</div>