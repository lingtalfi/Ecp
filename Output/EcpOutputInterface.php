<?php


namespace Ecp\Output;


interface EcpOutputInterface
{

    public function success($msg);

    public function getSuccess();
}