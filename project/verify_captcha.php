<?php
function verifyCaptchaResponse($response) {
    $secret = '6LcvrNYjAAAAAE4YOCd4EBAVP5FRcKKFth3I4TLt';
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_POST, 1);
    curl_setopt($request, CURLOPT_POSTFIELDS, 'secret='.$secret.'&response='.$response);
    curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($request);
    curl_close($request);

    return json_decode($data)->success;
}

?>