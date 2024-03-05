<?php
    require_once "./uitils/config.php";
    require_once "./uitils/commamn.php";
    // session_start();

    if(isset($_POST['usr-uname']) && isset($_POST['usr-email']) && isset($_POST['usr-phone']) && isset($_POST['usr-price']))
    {
        $name = $_POST['usr-uname'];
        $email = $_POST['usr-email'];
        $mobile = $_POST['usr-phone'];
        $amount = $_POST['usr-price'];

        // $_SESSION['name'] = $name;
        // $_SESSION['email'] = $email;
        // $_SESSION['phone'] = $mobile;


        $merchantId = MERCHANTIDUAT;

        $saltkey = SALTKEYUAT;

        $saltindex = SALTINDEX;

        $payload = array(
            "merchantId" => $merchantId,
            "merchantTransactionId" => "MT-" . getTransactionID(),
            "merchantUserId" => "M-" . uniqid(),
            "amount" => $amount*100,
            "redirectUrl" => BASE_URL . REDIRECTURL,
            "redirectMode" => "POST",
            "callbackUrl" => BASE_URL . REDIRECTURL,
            "mobileNumber" => $mobile,
            "paymentInstrument" => array(
                "type" => "PAY_PAGE",
            )
        );
        $jsonencode = json_encode($payload);
        $payloadbase64 = base64_encode($jsonencode);

        $payloaddata = $payloadbase64 . "/pg/v1/pay" . $saltkey;
        $sha256 = hash('sha256', $payloaddata);
        $checksum = $sha256 . '###' . $saltindex;
        $request = json_encode(array('request' => $payloadbase64));

        $url = '';
        if(API_STATUS == 'LIVE')
        {
            $url = LIVEURLPAY;
        }
        else
        {
            $url = UATURLPAY;
        }
        echo "</br>" . $url;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => 'CURLOPT_HTTP_VERSION_1_1',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-VERIFY: ". $checksum,
                "accept: application/json"
            ],
        ]);

        $responce = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if($err){
            echo "cURL ERROR #:" . $err;
        }else{
            $res = json_decode($responce);
            print_r($res);

            if(isset($res->success) && $res->success == '1')
            {
                $payUrl = $res->data->instrumentResponse->redirectInfo->url;
                header('Location:'. $payUrl);
            }
        }
    }

?>