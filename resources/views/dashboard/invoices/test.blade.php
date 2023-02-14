<input type="file" id="input"><br>
<img id="output">
<canvas id="canvas" style="display:none"></canvas>
<script src="http://marvel.com/assets/dashboard/eliteadmin-theme/assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
<script>
  // from http://stackoverflow.com/questions/19032406/convert-html5-canvas-into-file-to-be-uploaded
$(document).ready(function(){

  function uploadCanvas(dataURL) {
    var blobBin = atob(dataURL.split(',')[1]);
    var array = [];
    for(var i = 0; i < blobBin.length; i++) {
        array.push(blobBin.charCodeAt(i));
    }
    var file=new Blob([new Uint8Array(array)], {type: 'image/png'});
    var formdata = new FormData();
    formdata.append("image", file);

    $.ajax({
       url: "/asdfs/zlock",
       type: "POST",
       data: formdata,
       processData: false, // important
       contentType: false  // important
    }).complete(function(response){
      console.log(response.status);
    });
  }
  var input, canvas, context, output;

  input = document.getElementById("input");
  canvas = document.getElementById("canvas");
  context = canvas.getContext('2d');
  output = document.getElementById("output");

  input.addEventListener("change", function() {
    var reader = new FileReader();

    reader.addEventListener("loadend", function(arg) {
      var src_image = new Image();

      src_image.onload = function() {
        canvas.height = src_image.height;
        canvas.width = src_image.width;
        context.drawImage(src_image, 0, 0);
        var imageData = canvas.toDataURL("image/png");
        output.src = imageData;
        uploadCanvas(imageData);
      }

      src_image.src = this.result;
    });

    reader.readAsDataURL(this.files[0]);
  });
})
</script>
