<?php
    function user_agent(){
        $iPod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
        $iPhone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
        $iPad = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
        $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
        file_put_contents('./public/upload/install_log/agent',$_SERVER['HTTP_USER_AGENT']);
        if($iPad||$iPhone||$iPod){
            return 'ios';
        }else if($android){
            return 'android';
        }else{
            return 'pc';
        }
    }

    echo user_agent()."<BR>";

    clearstatcache();
    echo substr(sprintf('%o', fileperms('7815696ecbf1c96e6894b779456d330e.txt')), -4);

    echo "<br>";

    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    die($userAgent);
?>