<?php

namespace JsonRpc\Transport;

class HttpsClient {

    public $output = '';
    public $error = '';
    private $private_key = '';
    private $certificate = '';
    private $ca_certificate = '';
    private $ca_path = '';
    
    private $curl = '';

    public function __construct($private_key, $certificate, $ca_path) {
        $this->private_key = $private_key;
        $this->certificate = $certificate;
        $this->ca_path = $ca_path;

        $this->init_curl();
    }

    private function init_curl() {
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($this->curl, CURLOPT_SSLCERT, $this->certificate);
        curl_setopt($this->curl, CURLOPT_SSLKEY, $this->private_key);
        curl_setopt($this->curl, CURLOPT_CAINFO, $this->ca_path);
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    }

    public function send($method, $url, $json, $headers = array()) {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, 1);                //0 for a get request
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json ."\n");
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true); /* Will return data instead of true */
        
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($this->curl);

        if ($response === false) {
            throw new \Exception(sprintf('Unable to connect to %s Curl error: %d message: %s ', $url, curl_errno($this->curl), curl_error($this->curl)),curl_errno($this->curl));
        }

        $this->output = $response;
        return true;
    }

}
