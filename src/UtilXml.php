<?php
/**
 * Created by PhpStorm.
 * User: mybook-lhp
 * Date: 18/7/4
 * Time: 下午2:53
 */

namespace Tool\Utils;


class UtilXml
{
    /**
     * 获取微信XML数据并转为数组
     * @return array
     */
    static public function RequestXml()
    {
        $res_xml = file_get_contents("php://input");
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($res_xml, 'simpleXMLElement', LIBXML_NOCDATA)), true);
//        return json_decode('{"appid":"wx5c155e5a1ed471f8","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"N","mch_id":"1509522661","nonce_str":"80528f8adc248614cb9a41bab3f53c82","openid":"oNEL64rmg_vuGaiVWA81woTgZHcE","out_trade_no":"058488081865365351","result_code":"SUCCESS","return_code":"SUCCESS","sign":"5F9826D4DF9549CA4ED1444B5F714E18","time_end":"20180711180036","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200000156201807145073152203"}', true);
    }
}