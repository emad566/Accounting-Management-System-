<form method="POST" action="{{ route('clients.accountingPost') }}" class="" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    @if (Auth::user()->can(['Accountant']) || Auth::user()->can(['SupperAdmin']))
        <div id="city_idrow" class="row city_idrow">
            {!! select(['errors' => $errors, 'name' => 'state_id', 'frkName' => 'r_name', 'rows' => $states, 'transval' => 'اختر المحافظة', 'selected' => 4, 'label' => true, 'cols' => 4]) !!}
        </div>
    @endif

    <div class="row perion" id="period">
        {!! checkbox(['errors' => $errors, 'name' => 'is_period', 'id' => 'is_period', 'transval' => 'تحديد فترة', 'cols' => 4, 'class' => '', 'check' => false]) !!}
        {!! input(['errors' => $errors, 'name' => 'start_date', 'type' => 'date', 'value' => Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval' => 'من', 'label' => true, 'cols' => 4, 'attr' => 'disabled="disabled"']) !!}
        {!! input(['errors' => $errors, 'name' => 'end_date', 'type' => 'date', 'value' => Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval' => 'إلي', 'label' => true, 'cols' => 4, 'attr' => 'disabled="disabled"']) !!}
    </div>

    <div class="row showinvoices">
        {!! checkbox(['errors' => $errors, 'name' => 'is_invoices', 'transval' => 'عرض الفواتير في كشف الحساب', 'cols' => 12, 'class' => '']) !!}
        {{-- {!! checkbox(['errors'=>$errors, 'name'=>'all_clients',  'transval'=>'عرض كل العملاء بغض النظر عن المنطقة التي يعمل عليها المندوب', 'cols'=>12, 'class'=>'', 'check'=>false]) !!} --}}
    </div>

    <style>
        label.invoice_all {
            font-size: 18px;
            color: #000;
        }
    </style>

    <div id="client_idrow" class="row client_idrow">
        {!! select_client(['errors' => $errors, 'select_id'=>($client)? $client->id:'', 'name' => 'client_id', 'frkName' => 'client_name', 'rows' => $clients, 'transval' => 'اختر العميل', 'label' => true, 'required' => 'required', 'cols' => 12, 'attr' => 'data-live-search="true"']) !!}
        
    </div>
    <div class="row">
        {!! checkbox(['errors' => $errors, 'name' => 'invoice_all', 'id' =>'invoice_all', 'class' =>'invoice_all', 'transval' => 'عرض الفواتير المسددة بالكامل', 'cols' => 12, 'class' => '', 'check' => $invoice_all]) !!}
    </div>
    <div class="row">
        {!! buttonAction('', 'عرض كشف الحساب') !!}
    </div>
    
</form>
@if ($client)
    <div class="row relationElements">
        <h4 class="text-center bg-ingo col-md-12" style="border-bottom:solid 2px #ddd; padding:8px;">
            {{ $sarchPeriod }}</h4>
        {!! printData(['label' => 'العميل', 'data' => $client->client_name, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {!! printData(['label' => 'نوع العميل', 'data' => $client->client->client_type->name, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {!! printData(['label' => 'المحافظة', 'data' => $client->state, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {{-- @if ($client->city) --}}
        {!! printData(['label' => 'المدينة', 'data' => $client->city, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {{-- @endif
        @if ($client->r_name) --}}
        {!! printData(['label' => 'المنطقة', 'data' => $client->r_name, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {!! printData(['label' => 'العنوان', 'data' => $client->client_address, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {!! printData(['label' => 'الهاتف', 'data' => $client->client_phone, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {{-- @endif --}}

        {!! printData(['label' => ' محفظة العميل الحالية', 'data' => $client->view_client->get_overPrice_sum, 'cols' => 3, 'id' => '', 'class' => '']) !!}

        @if ($client_accounting)
            {{-- <div class="row accountingDetails"> --}}
            {!! printData(['label' => 'عدد التنزيلات', 'data' => $client_accounting->invoice_quantitys_sum, 'cols' => 3, 'id' => '', 'class' => '']) !!}
            {!! printData(['label' => 'عدد ما تم تحصيلة', 'data' => $client_accounting->get_quantitys_sum, 'cols' => 3, 'id' => '', 'class' => '']) !!}
            {!! printData(['label' => 'عدد المرتجع', 'data' => $client_accounting->return_quantitys_sum, 'cols' => 3, 'id' => '', 'class' => '']) !!}
            {!! printData(['label' => 'عدد المؤجل', 'data' => $client_accounting->next_quantity_sum, 'cols' => 3, 'id' => '', 'class' => '']) !!}


            {!! printData(['label' => 'رصيد إفتتاحي', 'data' => $client->client->initial_balance, 'cols' => 3, 'id' => '', 'class' => '']) !!}
            {!! printData(['label' => 'قيمة الفواتير', 'data' => $client_accounting->get_requireds_sum, 'cols' => 3, 'id' => '', 'class' => '']) !!}
            {!! printData(['label' => 'العميل دفع', 'data' => $client_accounting->client_pays_sum, 'cols' => 3, 'id' => '', 'class' => '']) !!}
            {!! printData(['label' => 'تحصيل فواتير', 'data' => $client_accounting->get_paids_sum, 'cols' => 3, 'id' => '', 'class' => '']) !!}
            <?php 
                $balance_finish = $client_accounting->get_nexts_sum - $client->view_client->get_overPrice_sum; 
            ?>
            {!! printData(['label' => 'باقي بعد خصم المحفظة  (صافي مديونية العميل)', 'data' => $balance_finish, 'cols' => 12, 'id' => '', 'class' => '']) !!}
            @if ($is_period)
                {!! printData(['label' => 'محفظة العميل في فترة البجث', 'data' => $client_accounting->client_balance_effect_sum, 'cols' => 3, 'id' => '', 'class' => '']) !!}
            @endif
            {{-- </div> --}}
        @endif
    </div>
    <p>العميل دفع = محفظة العميل الحالية + تحصيل فواتير</p>
    <p> باقي بعد خصم المحفظة  (صافي مديونية العميل) = قيمة الفواتير - العميل دفع</p> 
    <style>
        .head {
            padding: 5px;
            display: block;
            text-align: right;
            columns: #fff;
            font-size: 1.2em;
        }

        .acountantDiv {
            display: none;
        }
    </style>


    <?php $tmp_invoices = $invoices; ?>
    @if($invoice_all)
        @if ($is_invoices && $tmp_invoices)
            <?php $invoices = $tmp_invoices->where('get_nexts', '<>', 0); ?>

            @if ($invoices->count() > 0)
                <p style="background: red" class="head"> <i class="fas fa-eye" style="color: white"
                        aria-hidden="true"></i> فواتير أجل </p>
                <div class="acountantDiv">
                    @include('dashboard.clients.accountingModelItem', ['accrodion'=>'accrodion1'])
                </div>
            @endif
        @endif

        @if ($is_invoices && $tmp_invoices)
            <?php $invoices = $tmp_invoices->where('get_nexts', '=', 0)->where('get_overPrice_sum', '>', '0'); ?>
            @if ($invoices->count() > 0)
                <p style="background: rgb(24, 216, 24)" class="head"> <i class="fas fa-eye" style="color: white"
                        aria-hidden="true"></i>فواتير تم السداد ولكن لم يتم تصفيه المحفظة </p>
                <div class="acountantDiv">
                    @include('dashboard.clients.accountingModelItem', ['accrodion'=>'accrodion2'])
                </div>
            @endif
        @endif

        @if ($is_invoices && $tmp_invoices)
            <?php $invoices = $tmp_invoices->where('get_nexts', '=', 0)->where('get_overPrice_sum', '<', '0'); ?>
            @if ($invoices->count() > 0)
                <p style="background: yellowgreen" class="head"> <i class="fas fa-eye" style="color: white"
                        aria-hidden="true"></i>فواتير رصيد محفة بالسالب بالرغم من تم السداد </p>
                <div class="acountantDiv">
                    @include('dashboard.clients.accountingModelItem', ['accrodion'=>'accrodion3'])
                </div>
            @endif
        @endif

        @if ($is_invoices && $tmp_invoices)
            <?php $invoices = $tmp_invoices->where('get_nexts', '=', 0)->where('get_overPrice_sum', '=', '0'); ?>
            @if ($invoices->count() > 0)
                <p style="background: yellow" class="head"> <i class="fas fa-eye" style="color: white"
                        aria-hidden="true"></i>فواتير تم السداد وتم تسوية المحفظة </p>
                <div class="acountantDiv">
                    @include('dashboard.clients.accountingModelItem', ['accrodion'=>'accrodion4'])
                </div>
            @endif
        @endif 
    @endif 
    
    
    @if ($is_invoices && $tmp_invoices)
        <?php $invoices = $tmp_invoices; ?>
        @if ($invoices->count() > 0)
            <p style="background: black; color:white" class="head"> <i class="fas fa-eye" style="color: white"
                    aria-hidden="true"></i>كل الفواتير</p>
            @include('dashboard.clients.accountingModelItem', ['accrodion'=>'accrodion5'])
        @endif
    @endif


@endif
