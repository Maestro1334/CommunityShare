<?php
    $filepath = "C:/xampp/htdocs/communityshare/app/cron/counter.txt";
    $i = file_get_contents($filepath);
    $i = (int) $i;
    $i++;
    file_put_contents($filepath, $i);