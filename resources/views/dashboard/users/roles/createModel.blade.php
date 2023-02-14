@include('dashboard.includes.alerts.success')
@include('dashboard.includes.alerts.errors')
<form class="form" action="{{route('roles.store')}}" method="POST">
    @csrf
    <input type='hidden' name='_method' value='post'>

    <div class="row">
        {!! input(['errors'=>$errors, 'name'=>'name', 'transval'=>'اسم الرتبه', 'maxlength'=>60, 'required'=>'required', 'cols'=>12]) !!}

        @if(!$permissions->isEmpty())
            <h5 class="col-xs-12 col-lg-12">الصلاحيات <hr> </h5>
            @foreach ($permissions as $permission)
                {!! checkbox(['errors'=>$errors, 'value'=>$permission->id, 'name'=>'permissions[]', 'transval'=>$permission->name_ar, 'cols'=>$cols, 'check'=>false]) !!}
            @endforeach
        @endif
    </div>
    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
