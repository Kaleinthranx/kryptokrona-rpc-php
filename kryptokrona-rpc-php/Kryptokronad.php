<?php

/*
    Author: Kaleinthranx
    https://github.com/kaleinthranx
*/

require_once __DIR__.'/Libs/RpcClient.php';

class Kryptokronad extends RpcClient {
    private function callGet(string $method, array $params) {
        return $this->callJSON($method, $params, '/json_rpc');
    }

    public function getBlockCount() {
        return $this->callGet('getblockcount', []);
    }

    public function getBlockHash(int $height) {
        return $this->callGet('on_getblockhash', [$height]);
    }

    public function getBlockTemplate(int $reserve_size, string $wallet_address) {
        $params = [
            'reserve_size'   => $reserve_size,
            'wallet_address' => $wallet_address,
        ];

        return $this->callGet('getblocktemplate', $params);
    }

    public function submitBlock(string $block_blob) {
        return $this->callGet('submitblock', [$block_blob]);
    }

    public function getLastBlockHeader() {
        return $this->callGet('getlastblockheader', []);
    }

    public function getBlockHeaderByHash(string $hash) {
        return $this->callGet('getblockheaderbyhash', ['hash' => $hash]);
    }

    public function getBlockHeaderByHeight(int $height) {
        return $this->callGet('getblockheaderbyheight', ['height' => $height]);
    }

    public function getCurrencyId() {
        return $this->callGet('getcurrencyid', []);
    }

    public function getBlocks(int $height) {
        return $this->callGet('f_blocks_list_json', ['height' => $height]);
    }

    public function getBlock(string $hash) {
        return $this->callGet('f_block_json', ['hash' => $hash]);
    }

    public function getTransaction(string $hash) {
        return $this->callGet('f_transaction_json', ['hash' => $hash]);
    }

    public function getTransactionPool() {
        return $this->callGet('f_on_transactions_pool_json', []);
    }
}