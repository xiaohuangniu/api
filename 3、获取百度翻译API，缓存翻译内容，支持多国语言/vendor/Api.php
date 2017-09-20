<?php
/*
 +----------------------------------------------------------------------
 + Title        : 接口核心类 - 语言翻译核心类
 + Author       : 小黄牛
 + Version      : V1.0.0.1
 + Initial-Time : 2017-9-18
 + Last-time    : 2017-9-19 + 小黄牛
 + Desc         : 
 +----------------------------------------------------------------------
*/

class Fanyi{
    
     /**
      * 开始翻译
      * @param string $content 需要翻译的字符串
      * @param string $start   起始语言，默认中文转英文
      * @param string $end     目标语言，默认中文转英文
      * @return array 获取结果
      */
     public function Obtain($content='', $start='中文', $end='英语'){
        if (empty($content)) return $this->returnEcho('01', '翻译的内容不允许为空');
        if (empty($start))   return $this->returnEcho('02', '起始语言不能为空');
        if (empty($end))     return $this->returnEcho('03', '转换语言不能为空');

        $start = $this->Language($start);
        if (!$start) return $this->returnEcho('04', '该起始语言暂不支持翻译');
        $end   = $this->Language($end);
        if (!$end)   return $this->returnEcho('05', '该目标语言暂不支持转换');

        $md5       = md5($content);
        $file_path = 'vendor/cache/' . $md5 . '/'; 
        
        # 检测目录是否存在
        if (!file_exists($file_path)) {
            mkdir($file_path);
            # 开始翻译
            $res = $this->exec($content, $start, $end);
            if (!$res) return $this->returnEcho('06', '翻译失败');
            file_put_contents($file_path . 'fanyi.cache', $res);
        } else {
            $res = file_get_contents($file_path . 'fanyi.cache');
        }

        return return $this->returnEcho('00', '成功', $res);
     }

     /**
      * 选择对应的语言编码
      * @param string $name 中文
      */
     protected function Language($name){
        $Lang = [
            'ara' => '阿拉伯语',
            'de'  => '德语',
            'ru'  => '俄语',
            'fra' => '法语',
            'kor' => '韩语',
            'nl'  => '荷兰语',
            'pt'  => '葡萄牙语',
            'jp'  => '日语',
            'th'  => '泰语',
            'wyw' => '文言文',
            'spa' => '西班牙语',
            'el'  => '希腊语',
            'it'  => '意大利语',
            'en'  => '英语',
            'yue' => '粤语',
            'zh'  => '中文',
        ];
        return array_search($name, $Lang);
     }

     /**
     * 执行文本翻译
     * @param string $content 要翻译的文本
     * @param string $start   起始语言
     * @param string $end     目标语言
     * @return bool|string 翻译失败:false 翻译成功:翻译结果
     */
    protected function exec($content, $start, $end) {
        $url  = "http://fanyi.baidu.com/v2transapi";
        $data = array (
                'from'  => $start,
                'to'    => $end,
                'query' => $content 
        );
        $data = http_build_query ( $data );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_REFERER, "http://fanyi.baidu.com" );
        curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:37.0) Gecko/20100101 Firefox/37.0' );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        
        $result = json_decode ( $result, true );
        
        if (!isset($result ['trans_result'] ['data'] ['0'] ['dst'])){
            return false; 
        }
        return $result ['trans_result'] ['data'] ['0'] ['dst'];
     }

     /**
      * 组合返回内容
      * @param string $code 状态码
      * @param mixed  $msg  返回说明
      * @param mixed  $data 返回内容
      */
     protected function returnEcho($code , $msg, $data=''){
        return [
            'code' => "'{$code}'",
            'msg'  => $msg,
            'data' => $data,
        ];
     }

}

