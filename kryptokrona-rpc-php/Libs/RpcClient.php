<?php

/*
    Author: Kaleinthranx
    https://github.com/kaleinthranx
*/

class RpcClient {
    private $rpcHost;
    private $rpcPort;
    private $rpcPassword;

    private $curlHandle;
    private $headers;
    private $statusCode;
    private $responseHeaders;
    private $responseBody;

    public function __construct(string $rpcHost, int $rpcPort, ?string $rpcPassword = null) {
        $this->rpcHost = $rpcHost;
        $this->rpcPort = $rpcPort;
        $this->rpcPassword = $rpcPassword;
        $this->curlHandle = curl_init();
        $this->headers = [];
        $this->statusCode = null;
        $this->responseHeaders = [];
        $this->responseBody = null;
    }

    public function callJSON(string $method, array $params = [], string $path = '', ?array $extraData = null) {
		$request = [
			'jsonrpc' => '2.0',
			'method' => $method,
			'params' => $params,
            'id' => 1
		];

		if (is_array($extraData)) {
            $request = array_merge($request, $extraData);
        }

		$requestJson = json_encode($request);

		$url = $this->rpcHost . ':' . $this->rpcPort;
		if (!empty($path)) {
			$url .= '/' . ltrim($path, '/');
		}
		curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt($this->curlHandle, CURLOPT_POST, true);
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $requestJson);
		curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);

		$httpHeaders = [
			'Content-Type: application/json'
		];
		if ($this->rpcPassword !== null) {
			$httpHeaders[] = 'Authorization: Basic ' . base64_encode(':' . $this->rpcPassword);
		}
		curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, array_merge($httpHeaders, $this->headers));

		$responseJson = curl_exec($this->curlHandle);
		$this->statusCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
		$this->responseHeaders = curl_getinfo($this->curlHandle);
		$this->responseBody = $responseJson;

		$curlError = curl_errno($this->curlHandle);
		if($curlError) {
            throw new Exception('cURL Error: '.$curlError);
        } else if($this->statusCode >= 400 && $this->statusCode <= 600) {
            throw new Exception('RPC Returned status code: '.$this->statusCode);
        } else {
            $response = json_decode($responseJson, true);

            if ($response['jsonrpc'] != '2.0' || isset($response['error'])) {
                throw new Exception('RPC Returned Error: '.$responseJson);
            } else {;
                return $response['result'] ?? [];
            }
        }
	}

    public function callAPI($httpMethod = 'POST', $path = '', $params = []) {
        $requestJson = json_encode($params);

        $url = $this->rpcHost . ':' . $this->rpcPort;
        if (!empty($path)) {
            $url .= '/' . ltrim($path, '/');
        }
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);

        if ($httpMethod === 'POST') {
            curl_setopt($this->curlHandle, CURLOPT_POST, true);
            curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $requestJson);
        } elseif ($httpMethod === 'GET') {
            curl_setopt($this->curlHandle, CURLOPT_HTTPGET, true);
        } elseif ($httpMethod === 'DELETE') {
            curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } elseif ($httpMethod === 'PUT') {
            curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $requestJson);
        }

        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);

        $httpHeaders = [
            'Content-Type: application/json',
        ];
        if ($this->rpcPassword !== null) {
            $httpHeaders[] = 'X-API-KEY: '.$this->rpcPassword ;
        }
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, array_merge($httpHeaders, $this->headers));

        $responseJson = curl_exec($this->curlHandle);
        $this->statusCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        $this->responseHeaders = curl_getinfo($this->curlHandle);
        $this->responseBody = $responseJson;

        $curlError = curl_errno($this->curlHandle);
        if($curlError) {
            throw new Exception('cURL Error: ' . $curlError);
        } else if($this->statusCode >= 400 && $this->statusCode <= 600) {
            throw new Exception('RPC Returned status code: '.$this->statusCode.($responseJson ? ' Body: '.$responseJson : ''));
        } else {
            return json_decode($responseJson, true);
        }
    }

    public function result() {
        return $this->responseBody;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function getHeaders() {
        return $this->responseHeaders;
    }

    public function hasHeader($header) {
        return isset($this->responseHeaders[$header]);
    }

    public function getBody() {
        return $this->responseBody;
    }

    public function addHeader($header) {
        $this->headers[] = $header;
    }
}