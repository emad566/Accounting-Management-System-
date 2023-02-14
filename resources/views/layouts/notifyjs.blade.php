<script>


//transfer Pusher
alert("Notif")
var channel = pusher.subscribe('TransferEventChannel');
channel.bind('App\\Events\\TransferEvt', function (data) {
  console.log("EMad ===========================")
  var  authId = $("#authId").attr("authId")
  // alert(authId)
  // alert(authId)

  if(data.user_ids.includes(authId))
  {
    alert(data.notif_html)

    $.get("{{ route('realtimenotifs') }}",
    {
    //   user_id: $("#getuserId").attr('getuserId'),
    },

    function(data, status){
      $("#realtimenotifs").html(data)
    });
  }

});


</script>
