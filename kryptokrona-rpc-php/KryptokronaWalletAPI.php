<?php

/*
    Author: Kaleinthranx
    https://github.com/kaleinthranx
*/

require_once __DIR__.'/Libs/RpcClient.php';

class KryptokronaWalletAPI extends RpcClient {
    private $g_defaultMixin = 3;
    private $g_defaultFeePerByte = 1.953125;
    private $g_defaultUnlockTime = 0;

    public function __construct(string $host, int $port, string $password, ?int $defaultMixin = null, ?float $defaultFeePerByte = null, ?int $defaultUnlockTime = null) {
        parent::__construct($host, $port, $password);

        if (is_int($defaultMixin)) {
            $this->g_defaultMixin = $defaultMixin;
        }
        if (is_float($defaultFeePerByte)) {
            $this->g_defaultFeePerByte = $defaultFeePerByte;
        }
        if (is_int($defaultUnlockTime)) {
            $this->g_defaultUnlockTime = $defaultUnlockTime;
        }
    }

    private function callGet(string $path) {
        return $this->callAPI('GET', $path);
    }

    private function callDelete(string $path) {
        return $this->callAPI('DELETE', $path, []);
    }

    private function callPost(string $path, array $data) {
        return $this->callAPI('POST', $path, $data);
    }

    private function callPut(string $path, array $data) {
        return $this->callAPI('PUT', $path, $data);
    }

    private function isHex($str) {
        $regex = '/^[0-9a-fA-F]+$/';
        return preg_match($regex, $str);
    }

    private function isNull($var){
        return $var !== null;
    }

    public function alive() {
        try {
            $this->status();
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function addresses() {
        return $this->callGet('addresses');
    }

    public function balance(?string $address = null) {
        $url = ($address) ? 'balance/'.$address : 'balance';
        return $this->callGet($url);
    }

    public function balances() {
        return $this->callGet('balances');
    }

    public function close() {
        return $this->callDelete('wallet');
    }

    public function create(string $filename, string $password, string $daemonHost = '127.0.0.1', int $daemonPort = 11898, bool $daemonSSL = false) {
        return $this->callPost('wallet/create', [
            'daemonHost' => $daemonHost,
            'daemonPort' => $daemonPort,
            'daemonSSL' => $daemonSSL,
            'filename' => $filename,
            'password' => $password
        ]);
    }

    public function createAddress() {
        return $this->callPost('addresses/create', []);
    }

    public function createIntegratedAddress(string $address, string $paymentId) {
        return $this->callGet('addresses/'.$address.'/'.$paymentId);
    }

    public function deleteAddress(string $address) {
        return $this->callDelete('addresses/'.$address);
    }

    public function deletePreparedTransaction(string $hash) {
        return $this->callDelete('transactions/prepared/'.$hash);
    }

    public function getNode() {
        return $this->callGet('node');
    }

    public function importAddress(string $privateSpendKey, int $scanHeight = 0) {
        return $this->callPost('addresses/import', [
            'privateSpendKey' => $privateSpendKey,
            'scanHeight' => $scanHeight
        ]);
    }

    public function importDeterministic(int $walletIndex = 0, int $scanHeight = 0) {
        return $this->callPost('addresses/import/deterministic', [
            'walletIndex' => $walletIndex,
            'scanHeight' => $scanHeight
        ]);
    }

    public function importKey(string $filename, string $password, string $privateViewKey, string $privateSpendKey, int $scanHeight = 0, string $daemonHost = '127.0.0.1', int $daemonPort = 11898, bool $daemonSSL = false) {
        return $this->callPost('wallet/import/key', [
            'daemonHost' => $daemonHost,
            'daemonPort' => $daemonPort,
            'daemonSSL' => $daemonSSL,
            'filename' => $filename,
            'password' => $password,
            'scanHeight' => $scanHeight,
            'privateViewKey' => $privateViewKey,
            'privateSpendKey' => $privateSpendKey
        ]);
    }

    public function importSeed(string $filename, string $password, string $mnemonicSeed, int $scanHeight = 0, string $daemonHost = '127.0.0.1', int $daemonPort = 11898, bool $daemonSSL = false) {
        return $this->callPost('wallet/import/seed', [
            'daemonHost' => $daemonHost,
            'daemonPort' => $daemonPort,
            'daemonSSL' => $daemonSSL,
            'filename' => $filename,
            'password' => $password,
            'scanHeight' => $scanHeight,
            'mnemonicSeed' => $mnemonicSeed
        ]);
    }

    public function importViewAddress(string $publicSpendKey, int $scanHeight = 0) {
        return $this->callPost('addresses/import/view', [
            'publicSpendKey' => $publicSpendKey,
            'scanHeight' => $scanHeight
        ]);
    }

    public function importViewOnly(string $filename, string $password, string $privateViewKey, string $address, int $scanHeight = 0, string $daemonHost = '127.0.0.1', int $daemonPort = 11898, bool $daemonSSL = false) {
        return $this->callPost('wallet/import/view', [
            'daemonHost' => $daemonHost,
            'daemonPort' => $daemonPort,
            'daemonSSL' => $daemonSSL,
            'filename' => $filename,
            'password' => $password,
            'scanHeight' => $scanHeight,
            'privateViewKey' => $privateViewKey,
            'address' => $address
        ]);
    }

    public function keys(string $address = '') {
        $url = $address ? 'keys/'.$address : 'keys';

        return $this->callGet($url);
    }

    public function keysMnemonic(string $address) {
        return $this->callGet('keys/mnemonic/'.$address);
    }

    public function open(string $filename, string $password, string $daemonHost = '127.0.0.1', int $daemonPort = 11898, bool $daemonSSL = false) {
        $params = [
            'daemonHost' => $daemonHost,
            'daemonPort' => $daemonPort,
            'daemonSSL' => $daemonSSL,
            'filename' => $filename,
            'password' => $password,
        ];

        return $this->callPost('wallet/open', $params);
    }

    public function prepareAdvanced(
        array $destinations,
        ?float $fee = null,
        ?float $feePerByte = null,
        ?int $mixin = null,
        ?array $sourceAddresses = null,
        ?string $paymentId = null,
        ?string $changeAddress = null,
        ?int $unlockTime = null,
        $extraData = null
    ) {
        array_walk($destinations, function(array $dst) {
            if (!isset($dst["address"]) || !is_string($dst["address"])) {
                throw new Exception("Must supply a wallet address in every destination.");
            }
            if (!isset($dst["amount"]) || !is_int($dst["amount"])) {
                throw new Exception("Must supply an amount in every destination.");
            }
        });

        if(!is_null($extraData) && !$this->isHex($extraData)) {
            if(!is_string($extraData)) {
                $extraData = json_encode($extraData);
            }
            $extraData = bin2hex($extraData);
        }

        $request = array_filter([
            "destinations" => $destinations,
            "mixin" => $mixin ?? $this->g_defaultMixin,
            "sourceAddresses" => $sourceAddresses,
            "paymentID" => $paymentId,
            "changeAddress" => $changeAddress,
            "unlockTime" => $unlockTime ?? $this->g_defaultUnlockTime,
            "extraData" => $extraData
        ], [$this, 'isNull']);

        if ($fee) {
            $request["fee"] = $fee;
        } else {
            $request["feePerByte"] = $feePerByte ?? $this->g_defaultFeePerByte;
        }


        return $this->callPost("transactions/prepare/advanced", $request);
    }

    public function sendAdvanced (
        array $destinations,
        ?float $fee = null,
        ?float $feePerByte = null,
        ?int $mixin = null,
        ?array $sourceAddresses = null,
        ?string $paymentId = null,
        ?string $changeAddress = null,
        ?int $unlockTime = null,
        $extraData = null
    ) {
        array_walk($destinations, function(array $dst) {
            if (!isset($dst["address"]) || !is_string($dst["address"])) {
                throw new Exception("Must supply a wallet address in every destination.");
            }
            if (!isset($dst["amount"]) || !is_int($dst["amount"])) {
                throw new Exception("Must supply an amount in every destination.");
            }
        });

        if(!is_null($extraData) && !$this->isHex($extraData)) {
            if(!is_string($extraData)) {
                $extraData = json_encode($extraData);
            }
            $extraData = bin2hex($extraData);
        }

        $request = array_filter([
            "destinations" => $destinations,
            "mixin" => $mixin ?? $this->g_defaultMixin,
            "sourceAddresses" => $sourceAddresses,
            "paymentID" => $paymentId,
            "changeAddress" => $changeAddress,
            "unlockTime" => $unlockTime ?? $this->g_defaultUnlockTime,
            "extraData" => $extraData
        ], [$this, 'isNull']);

        if ($fee) {
            $request["fee"] = $fee;
        } else {
            $request["feePerByte"] = $feePerByte ?? $this->g_defaultFeePerByte;
        }

        return $this->callPost('transactions/send/advanced', $request);
    }

    public function prepareBasic(
        string $address,
        float $amount,
        ?string $paymentId = null
    ) {
        $request = [
            'destination' => $address,
            'amount' => $amount
        ];

        if ($paymentId) {
            $request['paymentID'] = $paymentId;
        }

        return $this->callPost('transactions/prepare/basic', $request);
    }

    public function primaryAddress() {
        return $this->callGet('addresses/primary');
    }

    public function reset(int $scanHeight = 0) {
        $this->callPut('reset', ['scanHeight' => $scanHeight]);
    }

    public function save() {
        $this->callPut('save', []);
    }

    public function sendFusionAdvanced(
        string $address,
        array $sourceAddresses,
        ?int $mixin = null
    ) {
        $request = [
            'mixin' => $mixin ?? $this->g_defaultMixin,
            'sourceAddresses' => $sourceAddresses,
            'destination' => $address
        ];

        return $this->callPost('transactions/send/fusion/advanced', $request);
    }

    public function sendFusionBasic() {
        return $this->callPost('transactions/send/fusion/basic', []);
    }

    public function setNode(
        string $daemonHost = null,
        int $daemonPort = null,
        bool $daemonSSL = false
    ) {
        $request = [
            'daemonHost' => $daemonHost,
            'daemonPort' => $daemonPort,
            'daemonSSL' => $daemonSSL,
        ];

        $this->callPut('node', $request);
    }

    public function status() {
        return $this->callGet('status');
    }

    public function transactionByHash(string $hash) {
        return $this->callGet('transactions/hash/'.$hash);
    }

    public function transactionPrivateKey(string $hash) {
        return $this->callGet('/transactions/privatekey/'.$hash);
    }

    public function transactions(?int $startHeight = null, ?int $endHeight = null) {
        $url = 'transactions';
        if ($startHeight !== null) {
            $url .= '/'.$startHeight;
            if ($endHeight !== null) {
                $url .= '/'.$endHeight;
            }
        }
        return $this->callGet($url);
    }

    public function transactionsByAddress(string $address, int $startHeight = 0, ?int $endHeight = null) {
        $url = "transactions/address/{$address}/{$startHeight}";
        if ($endHeight) {
            $url .= '/'.$endHeight;
        }
        return $this->callGet($url);
    }

    public function unconfirmedTransactions(?string $address = null) {
        $url = $address ? 'transactions/unconfirmed/'.$address : 'transactions/unconfirmed';
        return $this->callGet($url);
    }

    public function validateAddress(string $address) {
        return $this->callPost('addresses/validate', ['address' => $address]);
    }
}