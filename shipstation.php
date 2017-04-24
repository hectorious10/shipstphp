<?php
public function shipstation($data = null){ //Allow ShipStation to Pull Order Information        
    $this->load->model('customer_model');
    $this->output->set_content_type('application_json');        
    $result = $this->customer_model->get($data);       
    foreach ($result as $res){          
    $fields = array(
        'OderKey'=>$res['Authorization'].$res['TransactID'],
        'OrderNumber'=> $res['OrderID'],
        'OrderDate'=>$res['OrderDate'],
        'OrderStatus'=>$res['OrderStatus'],
        'OrderTotal'=> $res['OrderTotal'],
        'shippingAmount'=> $res['ShippingAmt'],
        'customerId'=>$res['CustomerID'],
        'customerEmail'=>$res['Email'],
        'billTo'=> array (
            'Name'=>$res['FirstName'].' '.$res['LastName']),
        'ShipTo'=> array (
            'Name'=>$res['FirstName'].' '.$res['LastName'],
            'street1'=>$res['ShipAddress1'],
            'street2'=> null,
            'city'=> $res['ShipCity'],
            'state'=> $res['ShipState'],
            'postalcode'=> $res['ShipPostalCode'],
            'country'=> 'US'
            ),
        'Items'=> array(
            array (
                'SKU'=>$res['SKU'],
                'Name'=>$res['PName'],
                'Quantity'=> $res['Quantity'],
                'UnitPrice'=>$res['Price']
                )
            )
        );  
    }
    $field_string = json_encode($fields);    
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://ssapi.shipstation.com/orders/createorder");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Basic YWNlZjRhMDY4ZDllNGUwYTlmMmIzMTRkZmVjMDRlNDc6YTJhN2JkMWJkNjI0NDk3ZWEzNDkwZmQyNTE3MmMxYTA="
        ));
   
    $response = curl_exec($ch);
    curl_close($ch);
    }//shipstation function//------------------------------------------------------------------------------

public function htmlmail($fields = null, $authnfo = null, $emailtype = null){
        $config = Array('protocol' => 'sendmail', 'smtp_host' => 'mail.bewellfeelwell.com', 'smtp_port' => 25, 'smtp_user' => 'customercare@bewellfeelwell.com',
            'smtp_pass' => '3hT-WLBuJiWU', 'smtp_timeout' => '4', 'mailtype'  => 'html', 'charset'   => 'iso-8859-1');
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");    
        $this->email->from('customercare@bewellfellwell.com', 'Hector Garcia');
        $data = $fields . $authnfo;
        $this->email->to($fields['x_email']);  // replace it with receiver mail id
        $this->email->subject($fields['x_description']." Order Confirmation"); // replace it with relevant subject     
        $body = $this->load->view($emailtype.'.php',$data,TRUE);
        $this->email->message($body);   
        $this->email->send();
    }//------------------------------------------------------------------------------

    ?>