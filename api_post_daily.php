<?php

$data_array =  array(
      "customer"        => $user['User']['customer_id'],
      "payment"         => array(
            "number"         => $this->request->data['account'],
            "routing"        => $this->request->data['routing'],
            "method"         => $this->request->data['method']
      ),
);
$make_call = callAPI('POST', 'https://api.example.com/post_url/', json_encode($data_array));
$response = json_decode($make_call, true);
$errors   = $response['response']['errors'];
$data     = $response['response']['data'][0];
?>