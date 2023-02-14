<form method="POST" action="{{ route('stores.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')
    <div class="row">
        {!! input(['errors'=>$errors, 'name'=>'Store_Name', 'transval'=>'اسم المخزن', 'maxlength'=>50, 'required'=>'required', 'cols'=>12]) !!}
        {!! input(['errors'=>$errors, 'name'=>'Store_Place', 'transval'=>'مكان المخزن', 'maxlength'=>50, 'required'=>'required', 'cols'=>12]) !!}
    </div>

    <div class="row">
        {!! checkbox(['errors'=>$errors, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
    </div>

    <div class="relationElements">
            <h5 class="col-xs-12 col-lg-12">أضف مناطق يعمل فيها المخزن:<hr> </h5>
            <div id="city_idrow" class="row city_idrow">
                {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'label'=>true, 'cols'=>12 ]) !!}
            </div>
            <div id="region_idrow" class="row region_idrow"></div>

            <button type="button" id="addElement" class="btn btn-small btn-success my-1"><i class="fa fa-plus-circle"></i> أضف</button>

            <div class="col-12 table-responsive">
                <table id="productsTable" class="table table-hover table-bordered color-bordered-table purple-bordered-table">
                    <thead>
                        <th>#</th>
                        <th>المحافظة</th>
                        <th>المدينة</th>
                        <th>المنطقة</th>
                        <th>حذف</th>
                    </thead>
                    <tbody>
                        <?php $i=0; ?>
                        @if (old('regions'))
                            @foreach (old('regions') as $region)
                                <?php $region = App\Models\Region::find($region); ?>
                                @if($region)
                                    <tr id="rowRegion{{ $region->id }}">
                                        <td>{{ ++$i }}
                                            <input type="hidden" name="regions[]" value="{{ $region->id }}" id="region_{{ $region->id }}" class="region_{{ $region->id }} regions">
                                        </td>
                                        <td>{{ $region->get_state_name() }}</td>
                                        <td>{{ $region->get_city_name() }}</td>
                                        <td>{{ $region->get_region_name() }}</td>
                                        <td> <a href="#" delId="rowRegion{{ $region->id }}" class="elementDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>
                                    <tr>
                                @endif
                            @endforeach

                        @endif
                    </tbody>
                </table>
                <input type="hidden" value="{{ $i }}" id="lastCount">
            </div>
    </div>

    <div class="row relationElements">
        @if(!$users->isEmpty())
            <h5 class="col-xs-12 col-lg-12">مستخدمين لهم صلاحيات علي هذا المخزن <hr> </h5>
            <?php $i=0; ?>
            @foreach ($users as $user)
                <?php $check = (old('users') && in_array($user->id ,old('users')) )? true : false;?>
                {!! checkbox(['errors'=>$errors, 'value'=>$user->id, 'name'=>'users[]', 'transval'=>$user->fullName, 'cols'=>$cols, 'check'=>$check]) !!}
            @endforeach
        @endif
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
