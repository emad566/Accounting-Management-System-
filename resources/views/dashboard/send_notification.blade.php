<form method="get" action="send_notification">
    Title<input type="text" name="title">
    Message<input type="text" name="message">
    <!--Icon path<input type="text" name="icon">-->
    Token<input type="text" name="token">
    <input type="submit" value="Send notification">
    </form>
    
<?php

    if(@$_REQUEST['title']){
        $result =sendNotification([
            "token"=>$tokens,
            "body"=>@$_REQUEST['message'],
            "title"=>@$_REQUEST['title'],
            "icon"=>@$_REQUEST['icon'],
            "click_action"=>"https://shinerweb.com"
        ]);
        print_r($result);
    }

?>