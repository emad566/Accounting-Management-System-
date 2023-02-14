@if (isset($errors) && $errors->count() > 0)
    <div id="alertError" class="alert alert-dismissable alert-danger">
        <button type="button" class="close" data-dismss="alert" aria-label="close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>
            <?php
                if(is_object ($errors))
                {
            ?>
                @foreach($errors->all() as $error)
                    <li> {!! $error !!}</li>
                @endforeach
            <?php }else{?>
                <li> {!! $errors !!}</li>
            <?php } ?>
        </strong>
    </div><!--#  .alert alert-dismissable alert-sucess -->
@endif

@if(Session::has('error'))
    <div class="row mr-2 ml-2" >
            <button style="background: red; color:yellow" type="text" class="btn btn-lg btn-block btn-outline-danger mb-2"
                    id="type-error"><i class="fas fa-exclamation-triangle"></i> {{Session::get('error')}}
            </button>
    </div>
@endif
