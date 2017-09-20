<?php
/*
 +----------------------------------------------------------------------
 + Title        : 接口核心类 - 即时获取天气预报信息
 + Author       : 小黄牛
 + Version      : V1.0.0.1
 + Initial-Time : 2017-9-20
 + Last-time    : 这个文件最后修改的时间 + 修改人的名称
 + Desc         : 
 +----------------------------------------------------------------------
*/

class Weather{
     /**
      * 获取天气预报
      * @param string $city 地区名称（市）
      * @param int    $type 时间
      * @return array 查询结果
      */
     public function Obtain($city='', $type = 0){
        if (empty($city))           return $this->returnEcho('01', '查询城市不允许为空');
        if ($type > 5 || $type < 0) return $this->returnEcho('02', '查询日期最多只能为0-4');

        $url   = 'http://php.weather.sina.com.cn/xml.php?city='.urlencode(iconv('utf-8', 'gb2312', $city)).'&password=DJOYnieT8234jlsK&day='.$type;
        $json  =  json_encode(simplexml_load_string($this->https_request($url)));
        $array = json_decode($json, true);
        return $array['Weather'];
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
     * 发送CURL请求
     * @param string $url  请求网址
     * @param array  $data 请求内容
     * @return 抓取内容
     */
    protected function https_request($url, $data = null){
		# 初始化一个cURL会话
		$curl = curl_init();  
		
		# 设置请求选项, 包括具体的url
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  // 禁用后cURL将终止从服务端进行验证
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);            // 设置为post请求类型
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  // 设置具体的post数据
		}
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);		
		$response = curl_exec($curl);                       // 执行一个cURL会话并且获取相关回复
		curl_close($curl);                                  // 释放cURL句柄,关闭一个cURL会话
		
		return $response;
	}
}