<? echo json_encode($results);

function getRealFileNameFromHeaders($headers,$url=null){

    foreach($headers as $header=>$val)
    {
        if(strtolower($header) =='content-disposition'){
            $tmp_name = explode('=', $val);
            return trim($tmp_name[1],'";\'');
        }
    }
    $stripped_url = preg_replace('/\\?.*/', '', $url);
    return basename($stripped_url);
}

if (isset($results['data']['recipients'],$results['message']['pdf'])){

    //send email after to JSON response to prevent slowness in web service.
    App::import('Vendor', 'PHPMailer', array('file' => 'phpmailer' . DS . 'class.phpmailer.php'));

    $mail = new PHPMailer();
    $mail->From = 'no-reply@toolbox.luxurylink.com';
    $mail->FromName = 'no-reply@toolbox.luxurylink.com';

    if ($_SERVER['ENV'] == 'development' || ISSTAGE == true){
        $mail->AddAddress('oefada@luxurylink.com');
    }else {
        foreach($recipients as $email => $name)
        {
            $mail->AddAddress($email, $name);
        }
    }

    $mail->Subject = 'LOA Document Generated for '.$results['data']['Client']['companyName'];

    $msg =  'An LOA Document has been generated for '.$results['data']['Client']['companyName'] . "\n\n";
    $msg .=  'The document has been attached for your convenience. You may also download here:'. "\n";
    $msg .=  $results['message']['pdf']."\n\n";


    $url = $results['message']['pdfLocal'];

    App::import('Core', 'HttpSocket');
    $HttpSocket = new HttpSocket();
    $socketResults = $HttpSocket->get($url);


    if (isset($socketResults)){
        $pdfOutput = $HttpSocket->response['body'];
        $filename = getRealFileNameFromHeaders($HttpSocket->response['header'],$url);
        $mail->AddStringAttachment($pdfOutput, $filename, 'base64', 'application/pdf');
    }
    /*if ($pdfOutput = @file_get_contents($url)){
        $filename = get_real_filename($http_response_header,$url);
        $mail->AddStringAttachment($pdfOutput, $filename, 'base64', 'application/pdf');
    }*/
    $mail->Body = $msg;

    $result = $mail->Send();
}
