<div id="{{ $accrodion }}" class="row showInvoicesRow {{ $accrodion }}">
        <?php $count = 1; ?>
        @foreach ($invoices as $invoice)
        <div class="card col-xs-12 col-lg-12">
            <div class="card-header bg-info text-primary" id="heading{{ $count }}">
            <h5 class="mb-0">
                <?php
                $flag = ($count==1)? "true": "false";
                // $show = ($count==1)? "show": "";
                $show = ($count==1)? "": "";
                ?>
                <div class="row">
                    {!! printData(['label'=>'#', 'data'=>'<a href="'.route('invoices.show', $invoice->id).'">'.$invoice->invoice_code.'</a>', 'cols'=>1, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'التاريخ', 'data'=>$invoice->invoice_date, 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'القيمة', 'data'=>$invoice->get_requireds, 'cols'=>1, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'العميل دفع', 'data'=>$invoice->client_pay, 'cols'=>1, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'من محفظة العميل', 'data'=>$invoice->paid_from_client_balance_sum, 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'سداد', 'data'=>$invoice->get_paids, 'cols'=>1, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'أجل', 'data'=>$invoice->get_nexts, 'cols'=>1, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'إضافة لمحفظة العميل', 'data'=>$invoice->get_overPrice_sum, 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    <div class="col-md-1">
                        <button style="color: #fff;" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse{{ $count }}" aria-expanded="{{ $flag }}" aria-controls="collapse{{ $count }}">
                            <i class="fas fa-eye delEdit"></i> عرض
                        </button>
                    </div>
                </div>

            </h5>
            </div>
            <div id="collapse{{ $count }}" class="collapse {{ $show }}" aria-labelledby="heading{{ $count }}" data-parent="#{{ $accrodion }}">
            <div class="card-body">

                <div class="row">
                    <div class="col-lg-12">
                        @if($invoice->status->id <20 && $invoice->rep_user_id == Auth::id())
                            <a href="{{ route('invoices.destroy', [$invoice->id]) }}" class="btn btn-danger float-right btn-danger mx-1">حذف</a>
                        @endif

                        @if(!( Auth::user()->voucher_id != $invoice->voucher_id
                            || Auth::user()->voucher->voucher_status !=3
                            || $invoice->status->id == 20))
                        <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-warning float-right btn-info mx-1">تعديل</a>
                        <a href="{{ route('invoices.destroy', $invoice->id) }}" class="btn btn-danger float-right btn-info mx-1">حذف</a>
                        @endif

                        @if(Auth::user()->can(['Create gets']) && $invoice->status->id ==20 && $invoice->get_nexts >0)
                            <a href="{{ route('gets.create', $invoice->id) }}" class="btn btn-primary float-right btn-success mx-1">تحصيل</a>
                        @endif
                        @if(Auth::user()->can(['Create_return']) && $invoice->status->id ==20 && $invoice->get_nexts >0)
                            <a href="{{ route('returns.create', $invoice->id) }}" class="btn float-right btn-light mx-1">عمل مرتجع</a>
                        @endif
                    </div>
                </div>
                <hr>
                @php $cols = 3; @endphp


                <div class="row">
                    {!! printData(['label'=>'المندوب', 'data'=>$invoice->rep->name, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
                    {!! printData(['label'=>'ملاحظات', 'data'=>$invoice->invoice_details, 'cols'=>12, 'id'=>'', 'class'=>'', ]) !!}
                </div>
                <div class="showimage">
                    <img class="invoiceImage" src="{{ url($invoice->invoice->image_rel_path()) }}" alt="">
                </div>
                <div class="table-responsive">
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
                            <td>{{ $invoice->get_requireds }}</td>
                            <td></td>
                            <td>{{ $invoice->get_paids }}</td>
                            <td>{{ $invoice->get_nexts }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                @if($invoice->gets)
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item bg-light">
                                <a class="nav-link show active" id="home-tab{{ $count }}" data-toggle="tab" href="#home5{{ $count }}" role="tab" aria-controls="home5{{ $count }}" aria-expanded="true" aria-selected="true">
                                    <span class="hidden-sm-up">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </span>
                                    <span class="hidden-xs-down">التحصيلات</span>
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light btn-outline-success">
                                <a class="nav-link show" id="profile-tab{{ $count }}" data-toggle="tab" href="#profile5{{ $count }}" role="tab" aria-controls="profile{{ $count }}" aria-selected="false">
                                    <span class="hidden-sm-up">
                                        <i class="fas fa-exchange-alt"></i>
                                    </span>
                                    <span class="hidden-xs-down">المرتجعات</span>
                                </a>
                            </li>

                        </ul>
                        <div class="tab-content tabcontent-border p-20" id="myTabContent">
                            <div role="tabpanel" class="tab-pane active" id="home5{{ $count }}" aria-labelledby="home-tab{{ $count }}">
                                @foreach ($invoice->gets as $get)
                                <div class="getDiv my-0" style="border:solid 3px #ab8ce4">
                                    <p class="btn btn-primary d-block text-left">{{ $get->get_date }} (مندوب التحصيل: {{ $get->user->name }})  --> رقم السند : {{ $get->get_code }}</p>
                                    @if($get->pivotGetProducts->where('get_quantity', '>', 0)->count()>0)
                                    <table id="gets" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                                        <thead>
                                            <th>الصنف</th>
                                            <th>{{ trans('validation.attributes.runID') }}</th>
                                            <th>الكمية</th>
                                            <th>السعر</th>
                                        </thead>
                                        <tbody>
                                        @foreach ($get->pivotGetProducts->where('get_quantity', '>', 0) as $GetProduct)
                                        <tr>
                                            <td>{{ $GetProduct->invoice_product->product->Product_Name }}</td>
                                            <td>{{ $GetProduct->invoice_product->runID }}</td>
                                            <td>{{ $GetProduct->get_quantity }}</td>
                                            <td>{{ $GetProduct->view_get_product->get_price }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="totalTr">
                                            <th colspan="3">إجمــــالي</th>
                                            <td>{{ $get->view_get->get_price_sum }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    @endif
                                    <div class="row">
                                        {!! printData(['label'=>'العميل دفع: ', 'data'=>$get->client_pay, 'cols'=>4, 'id'=>'', 'class'=>'', ]) !!}
                                        {!! printData(['label'=>'تحصيل من محفظة العميل', 'data'=>$get->paid_from_client_balance, 'cols'=>4, 'id'=>'', 'class'=>'', ]) !!}
                                        {!! printData(['label'=>'ما تم إضافته لمحفظة العميل', 'data'=>$get->get_overPrice, 'cols'=>4, 'id'=>'', 'class'=>'', ]) !!}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="profile5{{ $count }}" role="tabpanel" aria-labelledby="profile-tab{{ $count }}">
                                @foreach ($invoice->returns as $return)
                                <div class="getDiv my-5" style="border:solid 3px #ab8ce4">
                                    <p class="btn btn-primary d-block text-left">{{ $return->return_date }} (مندوب المرتجعات: {{ $return->user->name }})</p>
                                    <table id="gets" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                                        <thead>
                                            <th>الصنف</th>
                                            <th>{{ trans('validation.attributes.runID') }}</th>
                                            <th>كمية المرتجع</th>
                                            <th>مرتجع البونس</th>
                                        </thead>
                                        <tbody>
                                        @foreach ($return->returnProducts as $returnProduct)
                                        <tr>
                                            <td>{{ $returnProduct->invoice_product->product->Product_Name }}</td>
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
                <div class="row">
                    @if(Auth::user()->can(['change_invoice_status']) && $invoice->invoice_status_id==1)
                    <a class="btn btn-wrning" href="{{ route('invoices.changestatus', ['invoice'=>$invoice->id, 'status'=>10]) }}">طلب تعديل</a>
                    @endif

                    @if(Auth::user()->can(['change_invoice_status']) && $invoice->invoice_status_id!=20)
                    <a class="btn btn-success mx-1" href="{{ route('invoices.changestatus', ['invoice'=>$invoice->id, 'status'=>20]) }}">موافقة</a>
                    @endif
                </div>
                @endif

            </div>
            </div>
        </div>
        <?php $count++; ?>
        @endforeach
    </div>