<?php
namespace ApigeeKongUtil;

use Symfony\Component\Yaml\Yaml;

class FileProcessor {
    public function getYaml($path) {
        try {
            return  Yaml::parse(file_get_contents($path));
        }
        catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function setYaml($path, $data) {
        try {
            $yaml = Yaml::dump($data);
            file_put_contents($path, $yaml);
        }
        catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function getXMLData($file_path) {
        if (!file_exists($file_path)) {
            return false;
        }
        $xml = simplexml_load_file($file_path);
        $name = $xml->getName();
        $xml = json_decode(json_encode((array)$xml), TRUE);
        $xml['name'] = $name;
        return $xml;
    }
}