<?php
    openlog($_SERVER['HTTP_HOST'], LOG_PID | LOG_PERROR, LOG_USER);

    $postdata = file_get_contents('php://input');
    $input = json_decode($input, true);
    syslog(LOG_INFO, "Updating: ". getcwd());
	echo "success";

    closelog();
