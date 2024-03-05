<?php
    require_once "./uitils/config.php";
    require_once "./uitils/commamn.php";
    // session_start();
    // print_r($_POST);
    if(isset($_POST['merchantId']) && isset($_POST['transactionId']) && isset($_POST['amount']))
    {
        $marchantId = $_POST['merchantId'];
        $transactionId = $_POST['transactionId'];
        $amount = $_POST['amount']/100;
        // $saltkey = '';

        // $name = $_SESSION['name'];
        // $email = $_SESSION['email'];
        // $mobile = $_SESSION['phone'];


        if(API_STATUS == 'LIVE')
        {
            $url = LIVESTATUSCHECKURL . $marchantId . "/" . $transactionId;
            $saltkey = SALTKEYLIVE;
            $saltindex = SALTINDEX;
        }
        else
        {
            $url = STATUSCHECKURL . $marchantId . "/" . $transactionId;
            $saltkey = SALTKEYUAT;
            $saltindex = SALTINDEX;
        }

        $st = "/pg/v1/status/" . $marchantId . "/" . $transactionId .$saltkey;
        $dataSha256 = hash('sha256', $st);
        $checksum = $dataSha256 . '###' . $saltindex;

        $headers = array(
            "Content-Type: application/json",
            "accept: application/json",
            "X-VERIFY: ". $checksum,
            "X-MERCHANT-ID: ". $marchantId
        );


        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, '0');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, '0');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $resp = curl_exec($curl);

        curl_close($curl);

        $responsePayment = json_decode($resp, true);

        echo "<pre>";
        print_r($responsePayment);
        echo "</pre>";



        $tran_id = $responsePayment['data']['transactionId'];
        $amount = $responsePayment['data']['amount'];
        if($responsePayment['success'] && $responsePayment['code'] == 'PAYMENT_SUCCESS')
        {
            // Send Email

            header('Location: ' . BASE_URL . 'success.php?tid=' . $tran_id . '&amount=' . $amount);
        }
    }
?>