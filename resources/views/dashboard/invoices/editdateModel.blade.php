<form method="POST" action="{{ route('invoices.updatedate', $invoice->id) }}" class="" id="loginform" enctype="">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    <input type='hidden' name='_method' value='PUT'>
    <input type='hidden' name='id' value='{{ $invoice->id }}'>

    <div class="row">
        {!! printData(['label' => 'المندوب', 'data' => $invoice->rep->fullName, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {!! printData(['label' => 'العميل', 'data' => $invoice->client->client_name, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {!! printData(['label' => 'المحافظة', 'data' => $invoice->client->view_client->state, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {!! printData(['label' => 'المدينة', 'data' => $invoice->client->view_client->city, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {!! printData(['label' => 'المنطقة', 'data' => $invoice->client->view_client->r_name, 'cols' => 3, 'id' => '', 'class' => '']) !!}
        {!! input(['errors' => $errors, 'edit' => $invoice, 'name' => 'invoice_date', 'type' => 'date', 'value' => Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr' => true, 'label' => true, 'required' => 'required', 'cols' => 3]) !!}
    </div>

    <div class="row">
        {!! buttonAction(false, 'حفظ التعديلات') !!}
    </div>
</form>
