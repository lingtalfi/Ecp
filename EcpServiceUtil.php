<?php


namespace Ecp;


use Ecp\Exception\EcpInvalidArgumentException;
use Ecp\Exception\EcpUserMessageException;
use Ecp\Output\EcpOutput;

class EcpServiceUtil
{


    public static function executeProcess(callable $process)
    {
        if (array_key_exists("action", $_GET)) {
            $action = $_GET['action'];
            $intent = (array_key_exists("intent", $_POST)) ? $_POST['intent'] : [];

            try {
                $ecpOutput = EcpOutput::create();
                $out = call_user_func($process, $action, $intent, $ecpOutput);

                if (null !== ($successMsg = $ecpOutput->getSuccess())) {
                    $out['$$success$$'] = $successMsg;
                } elseif (null !== ($errorMsg = $ecpOutput->getError())) {
                    $out['$$error$$'] = $errorMsg;
                }

            } catch (\Exception $e) {

                if ($e instanceof EcpInvalidArgumentException) {
                    $missing = $e->getMissingKey();
                    $out = [
                        '$$invalid$$' => "the $missing argument was not passed",
                    ];
                    self::onInvalidArgumentAfter($e);
                } elseif ($e instanceof EcpUserMessageException) {
                    $out = [
                        '$$error$$' => $e->getMessage(),
                    ];
                } else {
                    $out = [
                        '$$error$$' => "An unexpected error occurred. It has been logged and we're working on it!",
                    ];
                    self::onErrorAfter($e);

                }
            }
        } else {
            $out = [
                '$$invalid$$' => "the action identifier was not passed",
            ];
        }

        return $out;

    }


    public static function get($key, $throwEx = true, $default = null)
    {
        /**
         * ecp recommends that all params are passed via $_POST, except the action param.
         */
        $pool = $_POST;
        if (array_key_exists($key, $pool)) {
            $ret = $pool[$key];
            if ('true' === $ret) {
                $ret = true;
            }
            if ('false' === $ret) {
                $ret = false;
            }
            return $ret;
        }
        if (true === $throwEx) {
            throw EcpInvalidArgumentException::create()->setMissingKey($key);
        }
        return $default;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected static function onInvalidArgumentAfter(EcpInvalidArgumentException $e)
    {

    }

    protected static function onErrorAfter(\Exception $e)
    {

    }
}