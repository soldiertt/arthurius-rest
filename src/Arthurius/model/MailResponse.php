<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 18-03-17
 * Time: 11:14
 */

namespace Arthurius\model;


class MailResponse
{

    public $success = false;

    public $message = "";

    public function __construct($success, $message)
    {
        $this->success = $success;
        $this->message = $message;
    }
}