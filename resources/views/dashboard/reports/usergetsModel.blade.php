@foreach ( $user_clients_pay as $get)
    @if(($get->get_overPrice !=0 ||  $get->client_pay !=0  || $get->paid_from_client_balance !=0) && !($get->client_pay == 0 
        && $get->get_overPrice == $get->paid_from_client_balance)) 
        <div class="card col-xs-12 col-lg-12">
            <div class="card-header bg-info text-primary" id="heading{{ $count }}">
            <h5 class="mb-0">
                <?php
                $flag = ($count==1)? "true": "false";
                // $show = ($count==1)? "show": "";
                $show = ($count==1)? "": "";
                ?>
                <div class="row">
                    {!! printData(['label'=>'#', 'data'=>$get->get_code, 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'كود الفاتورة', 'data'=>"<a href='".route('invoices.show', $get->invoice->id)."'>".$get->invoice->invoice_code."</a>", 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'العميل', 'data'=>$get->invoice->client->client_name, 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'التاريخ', 'data'=>$get->get_date, 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}
                    {!! printData(['label'=>'تحصيل', 'data'=>$get->client_pay, 'cols'=>2, 'id'=>'', 'class'=>'ver accrodHead', ]) !!}

                    <div class="col-md-1">
                        <button style="color: #fff;" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse{{ $count }}" aria-expanded="{{ $flag }}" aria-controls="collapse{{ $count }}">
                            <i class="fas fa-eye delEdit"></i> عرض
                        </button>
                    </div>

                    <div class="col-md-1">
                        @if(Auth::user()->can('Delete_get'))
                            <a href="{{ route('gets.delete', [$get->id]) }}" class="btn btn-danger mx-2"> حذف </a>
                        @endif
                    </div>
                </div>

            </h5>
            </div>
            <div id="collapse{{ $count }}" class="collapse {{ $show }}" aria-labelledby="heading{{ $count }}" data-parent="#accordionusergets">
                <div class="card-body">

                    <div class="row">
                        <div class="getDiv my-0 table-responsive" style="border:solid 3px #ab8ce4">
                            @if($get->pivotGetProducts->where('get_quantity', '>', 0)->count()>0)
                            <p class="btn btn-primary d-block text-left">{{ $get->get_date }} (مندوب التحصيل: {{ $get->user->name }})  --> رقم السند : {{ $get->get_code }}</p>
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
                    </div>
                </div>
            </div>
        </div>
        <?php $count++; ?>
    @endif
@endforeach