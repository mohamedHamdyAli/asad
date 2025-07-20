<?php

use Illuminate\Support\Facades\Log;

/**
 * keys : success, fail, needActive, exit, blocked , failDeleted ,needAdminActivation
 */

function successReturnData($data = [], $msg = '')
{
    return response()->json([
        'key' => 'success',
        'data' => $data,
        'msg' => $msg,
        'code' => 200,
    ]);
}

function failReturnAuth($msg = '')
{
    return response()->json(['key' => 'not_auth', 'msg' => $msg, 'code' => 401]);
}

function returnSuccessMsg($msg = '')
{
    return response()->json([
        'key' => 'success',
        'msg' => $msg,
        'code' => 200,
    ]);
}

function failReturnMsg($msg = '', $code = 400)
{
    return response()->json(['key' => 'fail', 'msg' => $msg, 'code' => $code]);
}


function failReturnData($data = [])
{
    return response()->json(['key' => 'fail', 'data' => $data, 'code' => 400]);
}

function invalidData($data = [])
{
    return response()->json(['key' => 'Invalid data send', 'data' => $data, 'code' => 422]);
}

function failServerReturnMsg(string $message, $error = null, int $code = 500)
{
    return response()->json([
        'message' => $message,
        'error' => $error,
        'code' => $code,
    ], $code);
}

