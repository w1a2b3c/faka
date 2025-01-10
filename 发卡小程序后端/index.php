<?php
if(!file_exists('install/install.lock')){
    header("Location: /install");
}else{
    echo '小K网卡密系统 <a href="https://www.xkwo.com/">访问</a>';
}