@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل تحويل حساب مالي</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('banktransfers.index') }}" class="btn btn-primary float-right mx-2">كل تحويلات الحسابات المالية</a>
                <a href="{{ route('banktransfers.show', $banktransfer->id) }}" class="btn btn-primary float-right">عرض</a>
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
                    <h6>تعديل تحويل حساب مالي</h6>
                    <hr>
                    @php $cols = 3; @endphp
                    @include('dashboard.banktransfers.editModel')
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

