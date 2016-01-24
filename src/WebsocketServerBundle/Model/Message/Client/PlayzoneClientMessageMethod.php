<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 24.01.16
 * Time: 20:06
 */

namespace WebsocketServerBundle\Model\Message\Client;


class PlayzoneClientMessageMethod
{
    const CALL_ACCEPT = 'call_accept';
    const CALL_SEND = 'call_send';
    const CALL_DELETE = 'call_delete';
    const CALL_DECLINE = 'call_decline';
}