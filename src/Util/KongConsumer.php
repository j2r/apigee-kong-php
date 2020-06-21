<?php

namespace ApigeeKongUtil;

use ApigeeKongUtil\KongBase;

class KongConsumer extends KongBase {
    function __construct() {
        parent::__construct();
    }

    public function migrateConsumer($apps) {
        $consumers = [];
        $kongConsumerUrl = $this->kongConfig['url'] . 'consumers';
        foreach ($apps as $app) {
            $consumer = $this->processAppData($app);
            echo "<pre>";print_r($consumer);
            $consumerData = $this->http->postData($kongConsumerUrl, $consumer['data']);
            echo "<pre>";print_r($consumerData);
            // If consumer is not created skip the key passing.
            echo "++++";
            if (!is_array($consumerData)) continue;
            echo "---";

            if (!empty($consumer['data']['username'];)) {
                $consumers[] = $consumer['data']['username'];
            }
            foreach ($consumer['keys'] as $key) {
                $kongKeyAuthUrl = $kongConsumerUrl . '/' . $consumerData['id'] . '/key-auth/';
                $consumerKey = $this->http->postData($kongKeyAuthUrl, ['key' => $key]);
            }
        }
        return $consumers;
    }

    private function processAppData($data) {
        $consumer = [];
        foreach ($data['attributes'] as $attribute) {
            if ($attribute['name'] == 'DisplayName' ) {
                $consumer['data']['username'] = $attribute['value'];
            }
        }
        $consumer['data']['custom_id'] = $data['appId'];
        $consumer['data']['tags'][] = $data['developerId'];
        $consumer['keys'] = [];
        foreach($data['credentials'] as $credential) {
            $consumer['keys'][] = $credential['consumerKey'];
        }
        return $consumer;
    }
}