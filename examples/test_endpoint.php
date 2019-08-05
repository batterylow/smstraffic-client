<?php

$operation = $_POST['operation'] ?? 'send';

if ('status' === $operation) {
    echo '<?xml version="1.0"?><reply>'
       . '<submition_date>2012-12-11 12:12:12</submition_date>'
       . '<send_date>2012-12-12 12:12:12</send_date>'
       . '<last_status_change_date>2012-12-12 12:12:00</last_status_change_date>'
       . '<status>Delivered</status>'
       . '<error></error>'
       . '<sms_id>999999999999999999</sms_id>'
       . '</reply>';
} elseif ('account' === $operation) {
    echo '<?xml version="1.0"?><reply><account>99999</account></reply>';
} else {
    echo '<?xml version="1.0"?><reply>'
       . '<result>OK</result>'
       . '<code>0</code>'
       . '<description>queued 1 messages</description>'
       . '<message_infos><message_info><phone>79999999999</phone><sms_id>999999999999999999</sms_id></message_info>'
       . '</message_infos></reply>';
}

exit(0);
