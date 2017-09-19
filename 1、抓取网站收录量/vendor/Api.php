<?php
/*
 +----------------------------------------------------------------------
 + Title        : 接口核心类 - 即时获取网站收录量
 + Author       : 小黄牛
 + Version      : V1.0.0.1
 + Initial-Time : 2017-9-18
 + Last-time    : 这个文件最后修改的时间 + 修改人的名称
 + Desc         : 
 +----------------------------------------------------------------------
*/

class Included{
     protected $url; // 查询网址

     /**
      * 获取收录量
      * @param string $type 查询类型 baidu | 360 | sougou
      * @param string $url  查询网址
      * @return array 查询结果
      */
     public function Obtain($type='', $url=''){
        if (empty($type)) return $this->returnEcho('01', '查询类型不允许为空');
        if (empty($type)) return $this->returnEcho('02', '查询网址不允许为空');

        $this->url = $url;

        switch ($type){
            case 'baidu' : return $this->baidu();  break;  
            case '360'   : return $this->so();     break;
            case 'sougou': return $this->sougou(); break;
            default: return $this->returnEcho('03', '暂无该查询类型');
        }

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

     /**
      * 百度查询
      */
    protected function baidu(){
        $site_url = 'http://www.baidu.com/s?wd=site%3A';
        $all      = $site_url . $this->url;
        $content  = file_get_contents($all);
        $data     = explode('<div class="c-span21 c-span-last"><p><b>', $content);

        if (count($data) > 1) {
            $array    = explode('</b></p>', $data[1]);
            $num      = str_replace('个', '', str_replace('找到相关结果数约', '', $array[0]));
            return $this->returnEcho('00', '成功', $num);
        }
        return $this->returnEcho('00', '成功', 0);
    }

    /**
     * 360查询
     */   
    protected function so(){
        $site_url = 'https://www.so.com/s?q=site%3A';
        $all      = $site_url . $this->url;
        $content  = curl_https($all);
        $data     = explode('找到相关结果约', $content);
        if (count($data) > 1) {
            $array = explode('个</span>', $data[1]);
            return $this->returnEcho('00', '成功', $array[0]);
        }
        return $this->returnEcho('00', '成功', 0);
    }

    /**
     * 搜狗查询
     */   
    protected function sougou(){
        $site_url = 'https://www.sogou.com/web?query=site%3A';
        $all      = $site_url . $this->url;
        $content  = $this->Curl_Https($all);
        $data     = explode('<p class="sr-num">找到约', $content);
        if (count($data) > 1) {
            $array = explode('条结果</p>', $data[1]);
            return $this->returnEcho('00', '成功', $array[0]);
        }
        return $this->returnEcho('00', '成功', 0);
    }


    /**
     * 抓取https的网页内容
     * @param string $url 请求网址
     * @return 抓取内容
     */
    protected function Curl_Https($url, $data=''){ 
        $curl = curl_init();                           // 启动一个CURL会话  
        curl_setopt($curl, CURLOPT_URL, $url);         // 要访问的地址  
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查  
        curl_setopt($curl, CURLOPT_POST, 1);           // 发送一个常规的Post请求  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 不允许直接输出
        $tmpInfo = curl_exec($curl);                   // 执行操作  
        curl_close($curl);                             // 关闭CURL会话  
        return $tmpInfo;
    } 
}