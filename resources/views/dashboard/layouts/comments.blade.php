<?php
    $notifs = Auth::user()->notifs;
?>
<?php 
$now = \Carbon\Carbon::now();
$y= $now->year;
$m= $now->month;
$pd = \Carbon\Carbon::parse("$y-$m-7");
$firstOfMonth = $pd->isoFormat('YYYY-MM-DD');

$endOfMonth = \Carbon\Carbon::parse(\Carbon\Carbon::now())->endOfMonth()->isoFormat('YYYY-MM-DD');

$all_invoices = Auth::user()->invoices->where('invoice_status_id', 20)->where('created_at', '>=', $firstOfMonth)->where('created_at', '<=', $endOfMonth);
$later_invoices = $all_invoices->where('date_diff', '>', 0);
?>

<li class="nav-item">
<a class="nav-link dropdown-toggle waves-effect waves-dark" title="فواتير سجلت متأخرة / اجمالي الفواتير" style="color: yellow; font-size: 10px;">
    <span>{{ $later_invoices->count() }}/{{ $all_invoices->count() }}</span> 
    <span style="text-decoration: underline; display: inline">{{ $later_invoices->sum('date_diff') }}</span>
</a>
</li>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false"> <i class="fa fa-bell"></i>
        <div class="notify notifications">
            <span class="heartbit"></span>
            <span class="point"></span>
            @if(Auth::user()->notifsCount())
            <span class="notecount heartbit num" style="font-size: 10px;">{{ Auth::user()->notifsCount() }}</span>
            @endif
        </div>
    </a>

    <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown">
        <ul>
            <li>
                <div class="drop-title" style="text-align: right;">الأشعارات <span class="notecount">@if(Auth::user()->notifsCount())</span><span class="notecount badge badge-pill badge-info pull-right">{{ Auth::user()->notifsCount() }}</span>@endif</div>
            </li>
            <li>
                <div id="realtimenotifs" class="message-center">
                    <!-- Message -->
                    {!! auth::user()->notifs_html() !!}
                    <!-- Message -->
                </div>
            </li>
            <li>
                <a class="nav-link text-center link" href="{{ route('notifs.index') }}"> <strong>عرض كل الأشعارات</strong> <i class="fa fa-angle-right"></i> </a>
            </li>
        </ul>
    </div>
</li>


