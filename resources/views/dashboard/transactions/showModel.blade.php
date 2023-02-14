<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title"></h6>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        @if($transaction->status->id !=30 && $transaction->from_user_id == Auth::id())
                            <a href="{{ route('transactions.destroy', [$transaction->id]) }}" class="float-right btn btn-danger mx-1">حذف</a>
                            <a href="{{ route('transactions.edit', [$transaction->id]) }}" class="float-right btn btn-warning mx-1">تعديل</a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    @include('dashboard.includes.alerts.success')
                    @include('dashboard.includes.alerts.errors')
                </div>

                <div class="row showimage mb-3">
                    <img class="transactionImage" src="{{ url($transaction->image_rel_path()) }}" alt="">
                </div>

                <div class="row">
                    {!! printData(['label'=>'حساب العضو/المندوب', 'data'=>$transaction->from_user->name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    {!! printData(['label'=>'الحساب المالي', 'data'=>$transaction->bank->bank_name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    {!! printData(['label'=>'المبلغ', 'data'=>$transaction->amount, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    {!! printData(['label'=>'التاريخ', 'data'=>$transaction->transaction_date, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    {!! printData(['label'=>'الحالة', 'data'=>$transaction->status->name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                </div>
                <div class="row">
                    {!! printData(['label'=>'ملاحظات', 'data'=>$transaction->transaction_details, 'cols'=>12, 'id'=>'', 'class'=>'']) !!}
                </div>

                <div class="row">
                    <div class="col-md-4 my-2">
                    @if(Auth::user()->can(['change_transaction_status']) && $transaction->transaction_status_id ==10 )
                    <a class="btn btn-warning" href="{{ route('transactions.changestatus', ['transaction'=>$transaction->id, 'status'=>20]) }}">قيد المراجعة </a>
                    @endif
                    </div>

                    <div class="col-md-4 my-2">
                    @if(Auth::user()->can(['change_transaction_status']) && $transaction->transaction_status_id <=20 )
                    <a class="btn btn-success mx-1" href="{{ route('transactions.changestatus', ['transaction'=>$transaction->id, 'status'=>30]) }}">موافقة</a>
                    @endif
                    </div>

                    <div class="col-md-4 my-2">
                    @if(Auth::user()->can(['change_transaction_status']) && $transaction->transaction_status_id <=20 )
                    <a class="btn btn-danger mx-1" href="{{ route('transactions.changestatus', ['transaction'=>$transaction->id, 'status'=>40]) }}">رفض</a>
                    @endif
                    </div>

                </div>


            </div>
        </div>
    </div>
</div>