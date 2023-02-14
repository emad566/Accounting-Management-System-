<?php
    $returns = (@$is_returns)? $returns : $voucher->returns;
    $voucher_id = (@$is_returns) ? false : $voucher->id;
?>
@if($returns && $returns->count()>0)
            @if(@$is_returns != true)
            <div class="row">
                <h1 class="display-3 col-12 badge badge-lg badge-light  d-block" style="font-size: 20px;" >مرتجعات الأذن</h2>
            </div>
            @endif
            <div id="accordionVocherReturn" class="row">
                <?php $count = 1000; ?>
                @foreach ($returns as $return)
                <div @if($return->invoice->voucher_id != $voucher_id) style="background: radial-gradient(blue, transparent);" @endif class="col-12 my-0 card-header bg-danger text-primary" id="heading{{ $count }}">
                    <div class="row">
                        <?php
                        $flag = ($count==1000)? "true": "false";
                        // $show = ($count==1000)? "show": "";
                        $show = ($count==1000)? "": "";
                        ?>
                        <a style="cursor: pointer; color:#fff" target="_blank" class="col-md-12 col-lg-2 ver accrodHead collapsed" style="color: #fff"  data-toggle="collapse" data-target="#collapse{{ $count }}" aria-expanded="{{ $flag }}" aria-controls="collapse{{ $count }}">العميل: {{ $return->invoice->client->client_name }}</a>
                        <a style="cursor: pointer; color:#fff" target="_blank" class="col-md-12 col-lg-2 ver accrodHead collapsed" style="color: #fff"  data-toggle="collapse" data-target="#collapse{{ $count }}" aria-expanded="{{ $flag }}" aria-controls="collapse{{ $count }}">التاريخ : {{ $return->return_date }}</a>
                        <a style="cursor: pointer; color:#fff" target="_blank" class="col-md-12 col-lg-3 ver accrodHead collapsed" style="color: #fff"  data-toggle="collapse" data-target="#collapse{{ $count }}" aria-expanded="{{ $flag }}" aria-controls="collapse{{ $count }}">تاريخ السيستم: {{ $return->created_at->diffForHumans() }}</a>
                        <a target="_blank" class="col-md-12 col-lg-2 ver accrodHead" style="color: #fff" href="{{ route('invoices.show', $return->invoice_id) }}">فاتورة: {{ $return->invoice->invoice_code }}</a>
                        <a target="_blank" class="col-md-12 col-lg-2 ver accrodHead" style="color: #fff" href="{{ route('vouchers.show', $return->invoice->voucher_id) }}">إذن صرف: {{ $return->invoice->voucher->voucher_code }}</a>

                        <div class="col-md-1">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse{{ $count }}" aria-expanded="{{ $flag }}" aria-controls="collapse{{ $count }}">
                                <i class="fas fa-eye delEdit" style="font-size: 20px; color: #fff;"></i> عرض
                            </button>
                        </div>
                    </div>
                </div>
                <div id="collapse{{ $count }}" class="col-12 collapse {{ $show }}" aria-labelledby="heading{{ $count }}" data-parent="#accordionVocherReturn">

                    <div class="table-responsive">
                        <table id="gets" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                            <thead>
                                <th>الصنف</th>
                                {{-- <th>{{ trans('validation.attributes.runID') }}</th> --}}
                                <th>كمية المرتجع</th>
                                <th>مرتجع البونس</th>
                            </thead>
                            <tbody>
                            @foreach ($return->returnProducts as $returnProduct)
                            <tr>
                                <td>{{ $returnProduct->invoice_product->product->Product_Name }}</td>
                                {{-- <td>{{ $returnProduct->invoice_product->runID }}</td> --}}
                                <td>{{ $returnProduct->return_quantity }}</td>
                                <td>{{ $returnProduct->return_bounce }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php $count++; ?>
                @endforeach
            </div>
            @endif