<?php

/*
    Author: Kaleinthranx
    https://github.com/kaleinthranx
*/

require_once __DIR__.'/Libs/RpcClient.php';

class KryptokronaService extends RpcClient {
    private $g_servicePassword;

    public function __construct(string $host, int $port, string $password) {
        parent::__construct($host, $port);
        $this->g_servicePassword = $password;
    }

    private function callGet(string $method, array $params) {
        return $this->callJSON($method, $params, '/json_rpc', ['password'=>$this->g_servicePassword]);
    }

    private function isNull($var){
        return $var !== null;
    }

    public function reset(?int $scanHeight = null) {
        return $this->callGet('reset', array_filter(compact('scanHeight'), [$this, 'isNull']));
    }

    public function save() {
        return $this->callGet('save', []);
    }

    public function getViewKey() {
        return $this->callGet('getViewKey', []);
    }

    public function getSpendKeys(string $address) {
        return $this->callGet('getSpendKeys', ['address' => $address]);
    }

    public function getMnemonicSeed(string $address) {
        return $this->callGet('getMnemonicSeed', ['address' => $address]);
    }

    public function getStatus() {
        return $this->callGet('getStatus', []);
    }

    public function getAddresses() {
        return $this->callGet('getAddresses', []);
    }

    public function createAddress(?string $secretSpendKey = null, ?string $publicSpendKey = null) {
        return $this->callGet('createAddress', array_filter(compact('secretSpendKey', 'publicSpendKey'), [$this, 'isNull']));
    }

    public function deleteAddress(string $address) {
        return $this->callGet('deleteAddress', ['address' => $address]);
    }

    public function getBalance(string $address = '') {
        return $this->callGet('getBalance', ['address'=>$address]);
    }

    public function getBlockHashes(int $firstBlockIndex, int $blockCount) {
        $params = [
            'firstBlockIndex' => $firstBlockIndex,
            'blockCount'      => $blockCount,
        ];

        return $this->callGet('getBlockHashes', $params);
    }

    public function getTransactionHashes(
        int $blockCount,
        ?int $firstBlockIndex = null,
        ?string $blockHash = null,
        ?array $addresses = null,
        ?string $paymentId = null
    ) {
        if (is_null($firstBlockIndex) && is_null($blockHash)) {
            throw new Exception("Must supply either the firstBlockIndex or blockHash.");
        } else if (!is_null($firstBlockIndex) && !is_null($blockHash)) {
            throw new Exception("Cannot supply both firstBlockIndex and blockHash, one must be null.");
        }
        return $this->callGet('getTransactionHashes', array_filter(compact('blockCount', 'firstBlockIndex', 'blockHash', 'addresses', 'paymentId'), [$this, 'isNull']));
    }

    public function getTransactions(
        int $blockCount,
        ?int $firstBlockIndex = null,
        ?string $blockHash = null,
        ?array $addresses = null,
        ?string $paymentId = null
    ) {
        if (is_null($firstBlockIndex) && is_null($blockHash)) {
            throw new Exception("Must supply either firstBlockIndex or blockHash.");
        } else if (!is_null($firstBlockIndex) && !is_null($blockHash)) {
            throw new Exception("Cannot supply both firstBlockIndex and blockHash, one must be null.");
        }
        return $this->callGet('getTransactions', array_filter(compact('blockCount', 'firstBlockIndex', 'blockHash', 'addresses', 'paymentId'), [$this, 'isNull']));
    }

    public function getUnconfirmedTransactionHashes(?array $addresses = null) {
        return $this->callGet('getUnconfirmedTransactionHashes', array_filter(compact('addresses'), [$this, 'isNull']));
    }

    public function getTransaction(string $transactionHash) {
        return $this->callGet('getTransaction', ['transactionHash' => $transactionHash]);
    }

    public function sendTransaction(
        int $anonymity,
        array $transfers,
        int $fee,
        ?array $addresses = null,
        ?int $unlockTime = null,
        ?string $extra = null,
        ?string $paymentId = null,
        ?string $changeAddress = null
    ) {
        return $this->callGet('sendTransaction', array_filter(compact('anonymity', 'transfers', 'fee', 'addresses', 'unlockTime', 'extra', 'paymentId', 'changeAddress'), [$this, 'isNull']));
    }

    public function createDelayedTransaction(
        int $anonymity,
        array $transfers,
        int $fee,
        ?array $addresses = null,
        ?int $unlockTime = null,
        ?string $extra = null,
        ?string $paymentId = null,
        ?string $changeAddress = null
    ) {
        return $this->callGet('createDelayedTransaction', array_filter(compact('anonymity', 'transfers', 'fee', 'addresses', 'unlockTime', 'extra', 'paymentId', 'changeAddress'), [$this, 'isNull']));
    }

    public function getDelayedTransactionHashes() {
        return $this->callGet('getDelayedTransactionHashes', []);
    }

    public function deleteDelayedTransaction(string $transactionHash) {
        return $this->callGet('deleteDelayedTransaction', ['transactionHash' => $transactionHash]);
    }

    public function sendDelayedTransaction(string $transactionHash) {
        return $this->callGet('sendDelayedTransaction', ['transactionHash' => $transactionHash]);
    }

    public function sendFusionTransaction(
        int $threshold,
        int $anonymity,
        ?array $addresses = null,
        ?string $destinationAddress = null
    ) {
        return $this->callGet('sendFusionTransaction', array_filter(compact('threshold', 'anonymity', 'addresses', 'destinationAddress'), [$this, 'isNull']));
    }

    public function estimateFusion(int $threshold, ?array $addresses = null) {
        return $this->callGet('estimateFusion', array_filter(compact('threshold', 'addresses'), [$this, 'isNull']));
    }

    public function createIntegratedAddress(string $address, string $paymentId) {
        $params = [
            'address'   => $address,
            'paymentId' => $paymentId,
        ];

        return $this->callGet('createIntegratedAddress', $params);
    }

    public function getFeeInfo() {
        return $this->callGet('getFeeInfo', []);
    }
}