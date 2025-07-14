<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;

class Http_client {
    protected $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function request($method, $uri, $options = []) {
        try {
            $response = $this->client->request($method, $uri, $options);
            return [
                'status' => $response->getStatusCode(),
                'data' => json_decode($response->getBody(), true)
            ];
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return [
                'status' => 500,
                'data' => $e->getMessage()
            ];
        }
    }
}