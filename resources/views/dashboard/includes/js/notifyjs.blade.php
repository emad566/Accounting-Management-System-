<script>


//transfer Pusher
var channel = pusher.subscribe('TransferEventChannel');
channel.bind('App\\Events\\TransferEvt', function (data) {
  console.log(data)
  var  authId = parseInt($("#authId").attr("authId"))
  var  notif_view = $("#"+data.notif_view).length

  if(notif_view){
        location.reload();
    }

  if(data.user_ids.includes(authId))
  {
    $.ajax({
        url: "{{ route('notifs.realtimenotifs') }}",

        type: 'GET',
        cache:false,
        success: function(data){
            $("#realtimenotifs").html(data.notifs_html)
            $(".notecount").html(data.notifsCount)
        },
        error: function(xhr){
                alert(xhr.status+' '+xhr.statusText);
            }
    });



    // alert(data.notif_html)

  }

});


</script>
