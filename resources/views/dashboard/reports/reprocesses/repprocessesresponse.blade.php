
<style>
    #responseAcc>div>div.card-header{
        border: solid 1px #000;
        background: #ccc;
    }
    
    #transferAcc>div>div.card-header{
        background: blueviolet;
    }
    #transferAcc>div>div.card-header span{
        color: yellow;
    }
    #spendAcc>div>div.card-header{
        background: blueviolet;
    }
    #spendAcc>div>div.card-header span{
        color: yellow;
    }
    
    #transactionAcc>div>div.card-header{
        background: blueviolet;
    }
    #transactionAcc>div>div.card-header span{
        color: yellow;
    }

    #voucherAcc>div>div.card-header{
        background: blueviolet;
    }
    #voucherAcc>div>div.card-header span{
        color: yellow;
    }
    .datatem button{
        display: flex;
        justify-content: space-between
    }

    .card-header{
        transition: all 3s ease-in-out;
    }
    .notAcceptFirst {
        transition: all 3s ease-in-out;
        background: radial-gradient(green, transparent);
    }
    .notAccept{
        transition: all 3s ease-in-out;
        background: radial-gradient(black, transparent);
    }

</style>
<div class="responseAcc col-md-12" id="responseAcc">
    @if($transfers && $transfers->count()>0)
    <div class="card">
      <div class="card-header" id="headingTransfers">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseTransfers" aria-expanded="true" aria-controls="collapseTransfers">
            التحويلات المخزنية
          </button>
        </h2>
      </div>
  
      <div id="collapseTransfers" class="collapse" aria-labelledby="headingTransfers" data-parent="#responseAcc">
        <div class="datatem transferAcc accordion col-md-12" id="transferAcc">
            @foreach ($transfers as $transfer)
            <div class="card">
                <div class="card-header" id="headingtransfer{{ $transfer->id }}">
                    <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapsetransfer{{ $transfer->id }}" aria-expanded="false" aria-controls="collapsetransfer{{ $transfer->id }}">
                        <span>{{ $transfer->transfer_code }} </span>
                        <span> من: {{$transfer->storeFrom->Store_Name }}  </span>
                        <span> إلي: {{$transfer->storeTo->Store_Name }}  </span>
                        <span> التاريخ: {{ $transfer->transfer_date }}</span>
                        <span> تاريخ النظام: {{ $transfer->created_at->diffForHumans() }}</span>
                        <span>{{ $transfer->status->name }}</span>
                        <span>عرض <i class="fas fa-eye delEdit" style="font-size: 20px; color: #fff;" aria-hidden="true"></i></span>
                    </button>
                    </h2>
                </div>
                <div id="collapsetransfer{{ $transfer->id }}" class="collapse" aria-labelledby="headingtransfer{{ $transfer->id }}" data-parent="#transferAcc">
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                                <thead>
                                    <th>#</th>
                                    <th>الصنف</th>
                                    {{-- <th>{{ trans('validation.attributes.runID') }}</th> --}}
                                    <th>الكمية</th>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=0;
                                    ?>
                                    @foreach ($transfer->products as $product)
                                    <tr id="row{{ $product->id }}">
                                        <td>{{ ++$i }}</td>
                                        <td>{{ App\Models\Product::findOrFail($product->id)->Product_Name }}</td>
                                        {{-- <td>{{ $product->pivot->RunID }}</td> --}}
                                        <td>{{ $product->pivot->Quantity }}</td>
                                    <tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <input type="hidden" value="{{ $i }}" id="lastCount">
                        </div>
                    </div>

                    <div id="linkgroup" class="row">
                        @if(Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 10 && $canchangestatusTo2030)
                        <a class="btn btn-warning mx-2" href="{{ route('transfers.changestatus', ['transfer'=>$transfer->id, 'status_id'=>20]) }}">تغيير الي قيد الشحن</a>
                        @endif

                        @if(Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 20 && $canchangestatusTo2030)
                        <a class="btn btn-danger mx-2" href="{{ route('transfers.changestatus', ['transfer'=>$transfer->id, 'status_id'=>30]) }}">إلغاء/رفض استلام</a>
                        @endif

                        @if(Auth::user()->can(['change_transfer_status']) && $transfer->status->id == 20 && $canchangestatusTo40)
                        <a class="btn btn-success mx-2" href="{{ route('transfers.changestatus', ['transfer'=>$transfer->id, 'status_id'=>40]) }}">استلام امر التحويل</a>
                        @endif

                        @if($transfer->status->id == 40 && Auth::id() == 1)
                        <a class="btn btn-success mx-2" href="{{ route('transfers.changestatus', ['transfer'=>$transfer->id, 'status_id'=>-20]) }}">إرجع للشحن  مرة أخري</a>
                        @endif

                    </div>
                </div>
            </div>
            @endforeach
        </div>
      </div>
    </div>
    @endif

    @if($invoices && $invoices->count()>0)
    <div class="card">
      <div class="card-header" id="headingInvoices">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseInvoices" aria-expanded="false" aria-controls="collapseInvoices">
            التنزيلات 
          </button>
        </h2>
      </div>
      <div id="collapseInvoices" class="collapse" aria-labelledby="headingInvoices" data-parent="#responseAcc">
        <div class="datatem accordion accordion col-md-12" id="accordion">
            <?php $is_invoices =true; ?>
            {{ view('dashboard.vouchers.voucherInvoices', compact(['invoices', 'is_invoices'])) }}
        </div>
      </div>
    </div>
    @endif

    @if($returns && $returns->count()>0)
    <div class="card">
        <div class="card-header" id="headingReturns">
            <h2 class="mb-0">
            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseReturns" aria-expanded="false" aria-controls="collapseReturns">
                المرتجعات 
            </button>
            </h2>
        </div>
        <div id="collapseReturns" class="collapse" aria-labelledby="headingReturns" data-parent="#responseAcc">
            <div class="datatem accordionVocherReturn accordion col-md-12" id="accordionVocherReturn">
                <?php $is_returns = true; ?>
                {{ view('dashboard.vouchers.voucherReturns', compact(['returns', 'is_returns'])) }}
            </div>
        </div>
    </div>
    @endif

    @if($gets && $gets->count()>0)
        <?php $getsCount = 0;?>
        @foreach ( $gets as $get)
        @if(($get->get_overPrice !=0 ||  $get->client_pay !=0  || $get->paid_from_client_balance !=0) && !($get->client_pay == 0 
            && $get->get_overPrice == $get->paid_from_client_balance)) 
                <?php $getsCount++; ?>
            @endif
        @endforeach
            
        @if($getsCount>0)
        <div class="card">
            <div class="card-header" id="headingGets">
                <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseGets" aria-expanded="false" aria-controls="collapseGets">
                    التحصيلات
                </button>
                </h2>
            </div>
            <div id="collapseGets" class="collapse" aria-labelledby="headingGets" data-parent="#responseAcc">
                <div class="datatem accordionusergets accordion col-md-12" id="accordionusergets">
                    <?php $user_clients_pay = $gets; $count=1; $is_get=true; ?>
                    {{ view('dashboard.reports.usergetsModel', compact(['user_clients_pay', 'count', 'is_get'])) }}
                </div>
            </div>
        </div>
        @endif
    @endif

    @if($spends && $spends->count()>0)
    <div class="card">
      <div class="card-header" id="headingSpends">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseSpends" aria-expanded="false" aria-controls="collapseSpends">
            المصروفات
          </button>
        </h2>
      </div>
      <div id="collapseSpends" class="collapse" aria-labelledby="headingSpends" data-parent="#responseAcc">
        <div class="datatem spendAcc accordion col-md-12" id="spendAcc">
            @foreach ($spends as $spend)
            <div class="card">
                <div class="card-header" id="headingspend{{ $spend->id }}">
                    <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapsespend{{ $spend->id }}" aria-expanded="false" aria-controls="collapsespend{{ $spend->id }}">
                        <span>الفئة: {{ $spend->cat->cat_name }} </span>
                        <span>المبلغ: {{ $spend->spend_amount }} </span>
                        <span> التاريخ: {{$spend->spend_date }}  </span>
                        <span> الحالة: {{$spend->status->name }}  </span>
                        <span>تاريخ النظام: {{ $spend->created_at->diffForHumans() }}</span>
                        <span>عرض <i class="fas fa-eye delEdit" style="font-size: 20px; color: #fff;" aria-hidden="true"></i></span>
                    </button>
                    </h2>
                </div>
                <div id="collapsespend{{ $spend->id }}" class="collapse" aria-labelledby="headingspend{{ $spend->id }}" data-parent="#spendAcc">
                    {{ view('dashboard.spends.showModel', compact(['spend'])) }}
                </div>
            </div>
            @endforeach
        </div>
      </div>
    </div>
    @endif

    @if($transactions && $transactions->count()>0)
    <div class="card">
      <div class="card-header" id="headingTransactions">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTransactions" aria-expanded="false" aria-controls="collapseTransactions">
            التحويلات المالية
          </button>
        </h2>
      </div>
      <div id="collapseTransactions" class="collapse" aria-labelledby="headingTransactions" data-parent="#responseAcc">
        <div class="datatem transactionAcc accordion col-md-12" id="transactionAcc">
            @foreach ($transactions as $transaction)
            <div class="card">
                <div class="card-header" id="headingtransaction{{ $transaction->id }}">
                    <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapsetransaction{{ $transaction->id }}" aria-expanded="false" aria-controls="collapsetransaction{{ $transaction->id }}">
                        <span>الحساب المالي: {{ $transaction->bank->bank_name }} </span>
                        <span>المبلغ: {{ $transaction->amount }} </span>
                        <span> التاريخ: {{$transaction->transaction_date }}  </span>
                        <span> الحالة: {{$transaction->status->name }}  </span>
                        <span>تاريخ النظام: {{ $transaction->created_at->diffForHumans() }}</span>
                        <span>عرض <i class="fas fa-eye delEdit" style="font-size: 20px; color: #fff;" aria-hidden="true"></i></span>
                    </button>
                    </h2>
                </div>
                <div id="collapsetransaction{{ $transaction->id }}" class="collapse" aria-labelledby="headingtransaction{{ $transaction->id }}" data-parent="#transactionAcc">
                    {{ view('dashboard.transactions.showModel', compact(['transaction'])) }}
                </div>
            </div>
            @endforeach
        </div>
      </div>
    </div>
    @endif

    @if($vouchers && $vouchers->count()>0)
    <div class="card">
      <div class="card-header" id="headingVouchers">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseVouchers" aria-expanded="false" aria-controls="collapseVouchers">
            اذونات الصرف
          </button>
        </h2>
      </div>
      <div id="collapseVouchers" class="collapse" aria-labelledby="headingVouchers" data-parent="#responseAcc">
        <div class="datatem voucherAcc accordion col-md-12" id="voucherAcc">
            @foreach ($vouchers as $voucher)
            <div class="card">
                <div class="card-header" id="headingvoucher{{ $voucher->id }}">
                    <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapsevoucher{{ $voucher->id }}" aria-expanded="false" aria-controls="collapsevoucher{{ $voucher->id }}">
                        <a href="{{ route('vouchers.show', $voucher->id) }}">المخزن {{ $voucher->store->Store_Name }} </a>
                        <span>تاريخ النظام: {{ $voucher->created_at->diffForHumans() }}</span>
                        <span> التاريخ: {{$voucher->voucher_date }}  </span>
                        <span> الحالة: {{$voucher->status->name }}  </span>
                        <a href="{{ route('vouchers.show', $voucher->id) }}">عرض <i class="fas fa-eye delEdit" style="font-size: 20px; color: #fff;" aria-hidden="true"></i></a>
                    </button>
                    </h2>
                </div>
                <div id="collapsevoucher{{ $voucher->id }}" class="collapse" aria-labelledby="headingvoucher{{ $voucher->id }}" data-parent="#voucherAcc">
                    {{-- {{ view('dashboard.vouchers.showModel', compact(['voucher'])) }} --}}
                </div>
            </div>
            @endforeach
        </div>
      </div>
    </div>
    @endif

  </div>



