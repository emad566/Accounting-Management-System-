@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل طلب سند صرف</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('spends.index') }}" class="btn btn-primary float-right mx-2">كل سندات الصرف</a>
                <a href="{{ route('spends.show', $spend->id) }}" class="btn btn-primary float-right">عرض</a>
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
                <div class="card-body">
                    <h6>تعديل طلب سند صرف</h6>
                    <hr>
                    @php $cols = 3; @endphp
                    @include('dashboard.spends.editModel')
                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
<script>
    $('document').ready(function(){
        //Region JSCode
        function state_id(){
            if($('#state_id').val()){
                $.ajax({
                    url: '{{ route('regions.cities') }}',
                    data:{state_id:$('#state_id').val() @if(old('city_id')), edit_id:{{ old('city_id') }}@endif},
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token()}}'
                    },
                    cache: false,

                    success: function (data) {
                        $('#city_idrow>.city_id').remove()
                        data = data.replace("col-md-12", "col-md-4 col-sm-12");
                        $('div.region_id').remove()
                        $('#city_idrow').append(data)


                    },
                    error: function (xhr) {
                        alert("Error: - " + xhr.status + " " + xhr.statusText);
                    }
                });
            }

        }

        state_id()
        $('#state_id').change(function(){
            $("#region_id").remove();
            state_id()
        })

        after = '<div id="region_idrow" class="region_idrow col-md-6 col-sm-12 region_idrow"></div>';
        function city_id(){
            if($('#city_id').val()){
                $.ajax({
                    url: '{{ route('cities.regions') }}',
                    data:{city_id:$('#city_id').val() @if(old('region_id')), edit_id:{{ old('region_id') }}@endif},
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token()}}'
                    },
                    cache: false,

                    success: function (data) {
                        $('#region_idrow>.region_id').remove()
                        data = data.replace("col-md-12", "col-md-4 col-sm-12");
                        $('div.region_id').remove()
                        $('#city_idrow .region_idrow').remove()
                        $('#city_idrow').append(after)
                        $('#city_idrow #region_idrow').replaceWith(data)
                    },
                    error: function (xhr) {
                        alert("Error: - " + xhr.status + " " + xhr.statusText);
                    }
                });
            }
        }

        city_id()
        $(document).on("change", "#city_id", function(e){
            city_id()

        })
        //End Region JSCode
        
        $('div.recieve_user_id').hide()


        $(document).on('change', '#cat_id', function(){
            is_user = $("#cat_id option:selected").attr('is_user')
            if(is_user==1){
                $('div.recieve_user_id').show()
            }else{
                $('div.recieve_user_id').hide()
            }
        })

        // Start of Compress image locallay befor uploadeng
        const MAX_WIDTH = 360;
        const MAX_HEIGHT = 640;
        const MIME_TYPE = "image/jpeg";
        const QUALITY = 1;

        const input = document.getElementById("image");
        input.onchange = function(ev) {
            $("#previewInvoice").html('')
            const file = ev.target.files[0]; // get the file
            const blobURL = URL.createObjectURL(file);
            const img = new Image();
            img.src = blobURL;

            img.onerror = function() {
                URL.revokeObjectURL(this.src);
                // Handle the failure properly
                console.log("Cannot load image");
            };
            img.onload = function() {
                URL.revokeObjectURL(this.src);
                const [newWidth, newHeight] = calculateSize(img, MAX_WIDTH, MAX_HEIGHT);
                const canvas = document.createElement("canvas");
                canvas.width = newWidth;
                canvas.height = newHeight;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, newWidth, newHeight);
                canvas.toBlob(
                    (blob) => {
                        // Handle the compressed image. es. upload or save in local state
                        displayInfo('Original file', file);

                        displayInfo('Compressed file', blob);
                    },
                    MIME_TYPE,
                    QUALITY
                );
                document.getElementById("previewInvoice").append(canvas);
                $("#previewInvoice canvas").attr('id', 'canvasId')

            };


        };

        function calculateSize(img, maxWidth, maxHeight) {
            let width = img.width;
            let height = img.height;

            // calculate the width and height, constraining the proportions
            if (width > height) {
                if (width > maxWidth) {
                    height = Math.round((height * maxWidth) / width);
                    width = maxWidth;
                }
            } else {
                if (height > maxHeight) {
                    width = Math.round((width * maxHeight) / height);
                    height = maxHeight;
                }
            }
            return [width, height];
        }

        // Utility functions for demo purpose

        function displayInfo(label, file) {
            const p = document.createElement('p');
            p.innerText = `${label} - ${readableBytes(file.size)}`;
            document.getElementById('previewInvoice').append(p);
        }

        function readableBytes(bytes) {
            const i = Math.floor(Math.log(bytes) / Math.log(1024)),
                sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
        }


        $("#loginform").on('submit', function (e) {

            var canvas= document.getElementById('canvasId')
            var dataURL = canvas.toDataURL()
            // alert (dataURL.length)
            $("#imgData").val(dataURL)
        })

        // End of Compress image locallay befor uploadeng

    })
</script>
@endsection

