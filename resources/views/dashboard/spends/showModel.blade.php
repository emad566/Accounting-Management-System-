<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title"></h6>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        @if($spend->status->id !=30 && $spend->spend_user_id == Auth::id())
                            <a href="{{ route('spends.destroy', [$spend->id]) }}" class="float-right btn btn-danger mx-1">حذف</a>
                        @endif
                        @if($spend->status->id <30 && $spend->spend_user_id == Auth::id())
                            <a href="{{ route('spends.edit', [$spend->id]) }}" class="float-right btn btn-warning mx-1">تعديل</a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    @include('dashboard.includes.alerts.success')
                    @include('dashboard.includes.alerts.errors')
                </div>

                <div class="row showimage mb-3">
                    <img class="spendImage" src="{{ url($spend->image_rel_path()) }}" alt="">
                </div>

                <div class="row">
                    {!! printData(['label'=>'منشئ الطلب', 'data'=>$spend->spend_user->name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    {!! printData(['label'=>'الفئة', 'data'=>$spend->cat->cat_name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    {!! printData(['label'=>'المبلغ', 'data'=>$spend->spend_amount, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    {!! printData(['label'=>'التاريخ', 'data'=>$spend->spend_date, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    {!! printData(['label'=>'الحالة', 'data'=>$spend->status->name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    @if($spend->recieve_user_id)
                    {!! printData(['label'=>'المستفيد', 'data'=>$spend->recieve_user->name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    @endif
                </div>

                <div class="row my-1">
                    <p class="d-block">صرف من </p>
                    @if($spend->bank_id)
                    {!! printData(['label'=>'الحساب المالي', 'data'=>$spend->bank->bank_name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    @else
                    {!! printData(['label'=>'صندوق المندوب', 'data'=>$spend->spend_user->name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    @endif
                </div>

                <div class="row region">
                    @if($spend->state)
                    {!! printData(['transAttr'=>'state_id', 'data'=>$spend->state, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    @endif

                    @if($spend->city)
                    {!! printData(['transAttr'=>'city_id', 'data'=>$spend->city, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    @endif

                    @if($spend->r_name)
                    {!! printData(['transAttr'=>'region_id', 'data'=>$spend->r_name, 'cols'=>3, 'id'=>'', 'class'=>'']) !!}
                    @endif
                </div>

                <div class="row">
                    {!! printData(['label'=>'ملاحظات', 'data'=>$spend->spend_details, 'cols'=>12, 'id'=>'', 'class'=>'']) !!}
                </div>

                <div class="row">
                    <div class="col-md-4 my-2">
                    @if(Auth::user()->can(['change_spend_status']) && $spend->transaction_status_id ==10 )
                    <a class="btn btn-warning" href="{{ route('spends.changestatus', ['spend'=>$spend->id, 'status'=>20]) }}">قيد المراجعة </a>
                    @endif
                    </div>

                    <div class="col-md-4 my-2">
                    @if(Auth::user()->can(['change_spend_status']) && $spend->transaction_status_id <=20 )
                    <a class="btn btn-success mx-1" href="{{ route('spends.changestatus', ['spend'=>$spend->id, 'status'=>30]) }}">موافقة</a>
                    @endif
                    </div>

                    <div class="col-md-4 my-2">
                    @if(Auth::user()->can(['change_spend_status']) && $spend->transaction_status_id <=20 )
                    <a class="btn btn-danger mx-1" href="{{ route('spends.changestatus', ['spend'=>$spend->id, 'status'=>40]) }}">رفض</a>
                    @endif
                    </div>

                </div>


            </div>
        </div>
    </div>
</div>