<?php


namespace Ecp\Output;


class EcpOutput implements EcpOutputInterface
{
    private $successMsg;


    public function __construct()
    {
        $this->successMsg = null;
    }

    public static function create()
    {
        return new static();
    }


    public function success($msg)
    {
        $this->successMsg = $msg;
        return $this;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public function getSuccess()
    {
        return $this->successMsg;
    }
}