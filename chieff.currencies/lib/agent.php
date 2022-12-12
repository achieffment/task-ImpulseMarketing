<?php

namespace chieff\currencies;

class Agent {

    static public function currenciesGetter() {

        \Bitrix\Main\Loader::includeModule("chieff.currencies");
        $url = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=".date("d/m/Y");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status === 200) {
            $output = simplexml_load_string($output);
            foreach($output as $k => $v) {
                $arFields = [];
                $arFields["CODE"] = strval($v->CharCode);
                $arFields["COURSE"] = floatval(str_replace(",",".", $v->Value)) / intval($v->Nominal);
                $arFields["DATE"] = new \Bitrix\Main\Type\DateTime(ConvertTimeStamp(false, "FULL"));
                $res = \chieff\currencies\CurrenciesTable::Add($arFields);
                if (!$res->isSuccess()) {
                    self::writeLog($arFields);
                }
            }
        } else {
            self::writeLog("Ошибка запроса " . date() . "\n");
        }

        return "\chieff\currencies\Agent::currenciesGetter();";
    }

    public function writeLog($data) {
        if (is_array($data))
            $data = print_r($data, true) . "\n";
        $path = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/chieff.currencies";
        if (is_dir($path))
            file_put_contents($path . "/agentLog.txt", $data, FILE_APPEND);
        else {
            $path = $_SERVER["DOCUMENT_ROOT"] . "/local/modules/chieff.currencies";
            if (is_dir($path))
                file_put_contents($path . "/agentLog.txt", $data, FILE_APPEND);
        }
    }

}