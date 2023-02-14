@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">كشف حساب تحصيل مندوب</h4>
            
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                @if(Auth::user()->can(['Shaw_all_safemoney']))
                <a href="{{ route('reports.usergetsall') }}" class="btn btn-primary float-right">كل صناديق المندوبين</a>
                @endif
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- /End Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                @if($user->usergets)
                <div class="card-body">
                    <h6 class="card-title">صندوق المندوب</h6>
                    <div class="row">
                        {!! printData(['label'=>'العضو/المندوب', 'data'=>$user->name . ": (" . $user->fName . " " . $user->lName . ")" , 'cols'=>12, 'id'=>'', 'class'=>'', ]) !!}
                    </div>

                    <form id="searchForm" class="searchForm">
                        <div>               
                            <div id="getPeriod" class="row">
                                {!! input(['errors'=>$errors, 'name'=>'from_search_date', 'type'=>'date', 'value'=>$search_period['from_search_date']? $search_period['from_search_date'] : (new Carbon\Carbon('2021-01-01'))->isoFormat('YYYY-MM-DD'), 'transval'=>'من', 'label'=>true, 'cols'=>2]) !!}
                                {!! input(['errors'=>$errors, 'name'=>'to_search_date', 'type'=>'date', 'value'=>$search_period['to_search_date']? $search_period['to_search_date'] : Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'إالي', 'label'=>true, 'cols'=>2]) !!}
                            </div>
                        </div>
                        @if($search_period['from_search_date'] && $search_period['to_search_date'] )
                        <div class="row"style="border: solid 3px black; padding-top:5px;">
                            {!! printData(['label'=>'خلال فترة البحث المختارة في الفترة من ', 'data'=> $search_period['from_search_date'] .'  إالي '. $search_period['to_search_date'], 'cols'=>12, 'id'=>'', 'class'=>'', ]) !!}
                            {!! printData(['label'=>'تحصيلات', 'data'=>$q_response['gets'], 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
                            {!! printData(['label'=>'تحويلات مالية', 'data'=>$q_response['transactions'], 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
                            {!! printData(['label'=>'مصروفات', 'data'=>$q_response['spends'], 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
                        </div>
                        @endif

                        <div class="row">
                            {!! buttonAction('', 'بحث <i class="fab fa-searchengin"></i>', 'searchbtn', false) !!}
                        </div>
                    </form>


                    <br>



                    <div class="row" style="border: solid 2px black; padding-top:5px;">
                        {!! printData(['label'=>'بدءا من تاريخ العمل علي السيستم حتي اليوم', 'data'=>"", 'cols'=>12, 'id'=>'', 'class'=>'', ]) !!}
                        {!! printData(['label'=>'تحصيلات', 'data'=>$user->usergets->user_gets, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
                        {!! printData(['label'=>'تحويلات مالية', 'data'=>$user->usergets->user_transaction_amounts, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
                        {!! printData(['label'=>'مصروفات', 'data'=>$user->usergets->user_spend_amounts, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
                        {!! printData(['label'=>'رصيد صندوق الخزينة', 'data'=>$user->usergets->user_safer_balance, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-8"></div>
                        <div class="col-md-2">
                            @if(Auth::user()->can(['CRUD_spend']) && Auth::user()->can(['Delegate']) && Auth::user()->id == $user->id)
                            <a href="{{ route('transactions.create') }}" class="btn btn-info float-right">تحويل ماليات</a>
                            @endif
                        </div>
                        <div class="col-md-2">
                            @if(Auth::user()->can(['CRUD_spend']) && Auth::user()->id == $user->id)
                            <a href="{{ route('spends.create') }}" class="btn btn-info float-right">مصروفات</a>
                            @endif
                        </div>
                    </div>

                    @if($user)
                    <h4>خلال فترة البحث المختارة في الفترة من {{ $search_period['from_search_date'] }} إالي {{ $search_period['to_search_date'] }}</h4>
                    <div id="accordionusergets" class="row showInvoicesRow accordion accordionusergets">
                        <?php $count = 1; 
                            $user_clients_pay = $user->gets->where('client_pay', '>', 0);
                            if($search_period['from_search_date'] && $search_period['to_search_date'] ){
                                $user_clients_pay = $user_clients_pay->where('get_date', '>=', $search_period['from_search_date'])->where('get_date', '<=', $search_period['to_search_date']);
                            }
                        ?>
                        
                        @include('dashboard.reports.usergetsModel')
                    </div>
                    @endif

                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
@endsection

