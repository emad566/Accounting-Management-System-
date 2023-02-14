@include('dashboard.includes.alerts.success')
@include('dashboard.includes.alerts.errors')
<form class="form" action="{{route('permissions.store')}}" method="POST">
    @csrf
    <input type='hidden' name='_method' value='post'>

    <div class="row">
        {!! input(['errors'=>$errors, 'name'=>'name', 'transval'=>'اسم الصلاحية', 'maxlength'=>60, 'required'=>'required', 'cols'=>12]) !!}

        @if(!$roles->isEmpty())
            <h5 class="col-xs-12 col-lg-12">الرتب <hr> </h5>
            @foreach ($roles as $role)
                {!! checkbox(['errors'=>$errors, 'value'=>$role->id, 'name'=>'roles[]', 'transval'=>$role->name, 'cols'=>$cols, 'check'=>false]) !!}
            @endforeach
        @endif

    </div>
    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
