<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">فاتورة عميل رقم: {{ $invoice->invoice_code }}
                    ({{ $invoice->status->name }})</h6>
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        if (!$invoice->client->is_first_add) {
                            $accept_title = 'موافقة العميل وموافقة الفاتورة';
                            $delete_title = 'حذف العميل وحذف الفاتورة';
                        } else {
                            $accept_title = 'موافقة';
                            $delete_title = 'حذف القاتورة بالكامل بتحصيلاتها ومرتجعاتها';
                        }
                        ?>

                        @if (($invoice->status->id < 20 && $invoice->rep_user_id == Auth::id()) || Auth::user()->can('delete_accepted_invoices'))
                            <a href="{{ route('invoices.destroy', [$invoice->id]) }}"
                                class="btn btn-danger float-right btn-danger mx-1">{{ $delete_title }}</a>
                        @endif

                        @if (!(Auth::user()->voucher_id != $invoice->voucher_id || Auth::user()->voucher->voucher_status != 3 || $invoice->status->id == 20))
                            <a href="{{ route('invoices.edit', $invoice->id) }}"
                                class="btn btn-warning float-right btn-info mx-1">تعديل</a>
                            <a href="{{ route('invoices.destroy', $invoice->id) }}"
                                class="btn btn-danger float-right btn-info mx-1">'حذف القاتورة بالكامل بتحصيلاتها ومرتجعاتها'</a>
                        @endif
                        
                        @if(@$is_get != true)
                            @if (Auth::user()->can(['Create gets']) && $invoice->status->id == 20 && $invoice->view_invoice->get_nexts > 0)
                                <a href="{{ route('gets.create', $invoice->id) }}"
                                    class="btn btn-info float-right btn-success mx-1">تحصيل</a>
                            @endif
                            @if (Auth::user()->can(['Create_return']) && $invoice->status->id == 20 && $invoice->view_invoice->get_nexts > 0)
                                <a href="{{ route('returns.create', $invoice->id) }}"
                                    class="btn btn-light float-right btn-light mx-1">عمل مرتجع</a>
                            @endif
                        @endif 
                        @if (Auth::user()->can(['update_invoice_date']))
                            <a href="{{ route('invoices.editdate', $invoice->id) }}"
                                class="btn btn-light float-right btn-info mx-1">تعديل تاريخ الفاتورة</a>
                        @endif
                    </div>
                </div>
                <hr>
                @php $cols = 3; @endphp
                @include('dashboard.includes.alerts.success')
                @include('dashboard.includes.alerts.errors')

                <div class="row"
                    @if (!$invoice->client->is_first_add) style= "background:red; padding-top:10px;" @endif>
                    {!! printData(['label' => 'المندوب', 'data' => $invoice->rep->fullName, 'cols' => 3, 'id' => '', 'class' => '']) !!}
                    {!! printData(['label' => 'العميل', 'data' => $invoice->client->client_name, 'cols' => 3, 'id' => '', 'class' => '']) !!}
                    {{-- {!! printData(['label'=>'نوع السداد', 'data'=>'', 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!} --}}
                    {!! printData(['label' => 'التاريخ', 'data' => $invoice->invoice_date, 'cols' => 3, 'id' => '', 'class' => '']) !!}
                </div>
                <div class="row">
                    {!! printData(['label' => 'المحافظة', 'data' => $invoice->client->view_client->state, 'cols' => 3, 'id' => '', 'class' => '']) !!}
                    {!! printData(['label' => 'المدينة', 'data' => $invoice->client->view_client->city, 'cols' => 3, 'id' => '', 'class' => '']) !!}
                    {!! printData(['label' => 'المنطقة', 'data' => $invoice->client->view_client->r_name, 'cols' => 3, 'id' => '', 'class' => '']) !!}
                </div>
                <div class="row">
                    {!! printData(['label' => 'ملاحظات', 'data' => $invoice->invoice_details, 'cols' => 12, 'id' => '', 'class' => '']) !!}
                </div>
                <div class="table-responsive">
                    <div class="showimage">
                        <img id="invoiceImage" class="invoiceImage" src="{{ url($invoice->image_rel_path()) }}" alt="">
                    </div>
                    <div><a href="#" id="rotateImg" rotatedId="invoiceImage"><i style="font-size: 50px" class="fas fa-sync-alt"></i></a></div>
                    <table id="productsTable"
                        class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                        <thead>
                            <th>#id</th>
                            <th>الصنف</th>
                            {{-- <th>{{ trans('validation.attributes.runID') }}</th> --}}
                            {{-- <th>سعر الجمهور</th> --}}
                            <th>الكمية</th>
                            <th>خصم</th>
                            <th>القيمة</th>
                            <th>كمية السداد</th>
                            <th>سداد جم</th>
                            <th>أجل جم</th>
                            <th>صافي الكمية</th>
                            <th>بونص</th>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach ($invoice->view_invoice_products as $ip)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $ip->product->Product_Name }}</td>
                                    {{-- <td>{{ $ip->runID }}</td> --}}
                                    {{-- <td>{{ $ip->invoice_public_price }}</td> --}}
                                    <td>{{ $ip->invoice_quantity }}</td>
                                    <td>{{ $ip->discount }}</td>
                                    <td>{{ $ip->get_required }}</td>
                                    <td>{{ $ip->get_quantity }}</td>
                                    <td>{{ $ip->get_paid }}</td>
                                    <td>{{ $ip->get_next }}</td>
                                    <td>{{ $ip->invoice_net_q_withoutbounce }}</td>
                                    <td>{{ $ip->invoice_bounce_net }}</td>
                                </tr>
                                <?php $i++; ?>
                            @endforeach
                            <tr class="totalTr">
                                <th colspan="4">إجمــــالي</th>
                                <td>{{ $invoice->view_invoice->get_requireds }}</td>
                                <td></td>
                                <td>{{ $invoice->view_invoice->get_paids }}</td>
                                <td>{{ $invoice->view_invoice->get_nexts }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p>العميل دفع: <span style="color: blue;"
                        id="totalPaid">{{ $invoice->view_invoice->client_pay }}</span> جم</p>
                <p>تحصيل من محفظة العميل: <span style="color: blue;"
                        id="totalPaid">{{ $invoice->view_invoice->paid_from_client_balance_sum }}</span> جم</p>
                <p>ما تم إضافته لمحفظة العميل: <span
                        id="client_balance_diff">{{ $invoice->view_invoice->get_overPrice_sum }}</span> جم</p>
                <input type="hidden" value="{{ $i }}" id="lastCount">

                @if ($invoice->gets && @$is_get != true)
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item bg-light">
                                    <a class="nav-link show active" id="home-tab" data-toggle="tab" href="#home5"
                                        role="tab" aria-controls="home5" aria-expanded="true" aria-selected="true">
                                        <span class="hidden-sm-up">
                                            <i class="fas fa-hand-holding-usd"></i>
                                        </span>
                                        <span class="hidden-xs-down">التحصيلات</span>
                                    </a>
                                </li>
                                <li class="nav-item waves-effect waves-light btn-outline-success">
                                    <a class="nav-link show" id="profile-tab" data-toggle="tab" href="#profile5"
                                        role="tab" aria-controls="profile" aria-selected="false">
                                        <span class="hidden-sm-up">
                                            <i class="fas fa-exchange-alt"></i>
                                        </span>
                                        <span class="hidden-xs-down">المرتجعات</span>
                                    </a>
                                </li>

                            </ul>
                            <div class="tab-content tabcontent-border p-20" id="myTabContent">
                                <div role="tabpanel" class="tab-pane fade active show" id="home5"
                                    aria-labelledby="home-tab">
                                    @foreach ($invoice->gets as $get)
                                        <div class="getDiv my-0 table-responsive" style="border:solid 3px #ab8ce4">
                                            <p class="btn btn-primary d-block text-left">{{ $get->get_date }}
                                                (مندوب التحصيل: {{ $get->user->name }}) --> رقم السند :
                                                {{ $get->get_code }}</p>
                                            @if ($get->pivotGetProducts->where('get_quantity', '>', 0)->count() > 0)
                                                <table id="gets"
                                                    class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                                                    <thead>
                                                        <th>الصنف</th>
                                                        <th>{{ trans('validation.attributes.runID') }}</th>
                                                        <th>الكمية</th>
                                                        <th>السعر</th>
                                                        <th>حذف تحصيل علب</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($get->pivotGetProducts->where('get_quantity', '>', 0) as $GetProduct)
                                                            <tr>
                                                                <td>{{ $GetProduct->invoice_product->product->Product_Name }}
                                                                </td>
                                                                <td>{{ $GetProduct->invoice_product->runID }}
                                                                </td>
                                                                <td>{{ $GetProduct->get_quantity }}</td>
                                                                <td>{{ $GetProduct->view_get_product->get_price }}
                                                                </td>
                                                                <td>
                                                                    @if (Auth::user()->can('Delete_get'))
                                                                    <form  method="POST" action="{{ route('gets.deleteget') }}">
                                                                        @csrf
                                                                        <input type="number" style="width: 100px" max="{{ $GetProduct->get_quantity }}"  min="0" name="delGetQuantity" id="delGetQuantity">
                                                                        <input type="hidden" name="get_id" value="{{ $GetProduct->get_id }}">
                                                                        <input type="hidden" name="invoice_product_id" value="{{ $GetProduct->invoice_product_id }}">
                                                                        <button type="submit"><i class="fas fa-trash delEdit" aria-hidden="true"></i></button>
                                                                    </form>  
                                                                    @endif   
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr class="totalTr">
                                                            <th colspan="3">إجمــــالي</th>
                                                            <td>{{ $get->view_get->get_price_sum }}</td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            @endif
                                            <div class="row">
                                                {!! printData(['label' => 'العميل دفع: ', 'data' => $get->client_pay, 'cols' => 4, 'id' => '', 'class' => '']) !!}
                                                {!! printData(['label' => 'تحصيل من محفظة العميل', 'data' => $get->paid_from_client_balance, 'cols' => 4, 'id' => '', 'class' => '']) !!}
                                                {!! printData(['label' => 'ما تم إضافته لمحفظة العميل', 'data' => $get->get_overPrice, 'cols' => 4, 'id' => '', 'class' => '']) !!}
                                            </div>

                                            <div>
                                                @if (Auth::user()->can('Delete_get'))
                                                    <a href="{{ route('gets.delete', [$get->id]) }}"
                                                        class="btn btn-danger mx-2"> حذف كامل التحصيل</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="tab-pane fade" id="profile5" role="tabpanel"
                                    aria-labelledby="profile-tab">
                                    @foreach ($invoice->returns as $return)
                                        <div class="getDiv my-5" style="border:solid 3px #ab8ce4">
                                            <p class="btn btn-primary d-block text-left">
                                                {{ $return->return_date }} (مندوب المرتجعات:
                                                {{ $return->user->name }})</p>
                                            <table id="gets"
                                                class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                                                <thead>
                                                    <th>الصنف</th>
                                                    <th>{{ trans('validation.attributes.runID') }}</th>
                                                    <th>كمية المرتجع</th>
                                                    <th>مرتجع البونس</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($return->returnProducts as $returnProduct)
                                                        <tr>
                                                            <td>{{ $returnProduct->invoice_product->product->Product_Name }}
                                                            </td>
                                                            <td>{{ $returnProduct->invoice_product->runID }}</td>
                                                            <td>{{ $returnProduct->return_quantity }}</td>
                                                            <td>{{ $returnProduct->return_bounce }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row"  @if (!$invoice->client->is_first_add) style="background:red; margin:30px; padding:20px;"  @endif>
                        @if (Auth::user()->can(['change_invoice_status']) && $invoice->invoice_status_id != 20)
                            <a class="btn btn-warning"
                                href="{{ route('invoices.changestatus', ['invoice' => $invoice->id, 'status' => 10]) }}">طلب
                                تعديل</a>
                        @endif



                        @if (Auth::user()->can(['change_invoice_status']) && $invoice->invoice_status_id != 20)
                            <a class="btn btn-success mx-1"
                                href="{{ route('invoices.changestatus', ['invoice' => $invoice->id, 'status' => 20]) }}">{{ $accept_title }}</a>
                        @endif

                        @if (Auth::user()->can(['change_invoice_status_to_send']) && $invoice->invoice_status_id == 20)
                            <a class="btn btn-warning"
                                href="{{ route('invoices.changestatus', ['invoice' => $invoice->id, 'status' => 10]) }}">طلب
                                تعديل</a>
                        @endif


                        @if (Auth::user()->can('delete_accepted_invoices'))
                            <a href="{{ route('invoices.destroy', [$invoice->id]) }}"
                                class="btn btn-danger mx-2">{{ $delete_title }}</a>
                        @endif

                    </div>
                @endif

            </div>
        </div>
    </div>
</div>