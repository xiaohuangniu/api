<?php
/*
 +----------------------------------------------------------------------
 + Title        : 接口核心类 - 查询手机归属地
 + Author       : 小黄牛
 + Version      : V1.0.0.1
 + Initial-Time : 2017-9-20
 + Last-time    : 这个文件最后修改的时间 + 修改人的名称
 + Desc         : 
 +----------------------------------------------------------------------
*/

class Phone_ownership{
     /**
      * 执行查询
      * @param int $phone 手机号
      * @return array 查询结果
      */
     public function Obtain($phone=''){
        if (empty($phone))           return $this->returnEcho('01', '手机号不允许为空');

        $url  = 'https://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel='.$phone;
        $res  = iconv("GB2312","UTF-8", $this->https_request($url));
        $json = str_replace("__GetZoneResult_ = {", '', $res);
        $json = str_replace('	', '', $json);
        $json = str_replace(' ', '', $json);
        $json = str_replace('}', '', $json);
        $json = str_replace('
', '', $json);
        $json = explode(',', $json);
        $array = [];
        foreach ($json as $k => $v) {
            $arr = explode(':', $v);
            $key = $arr[0];
            $array[$key] = ltrim(rtrim($arr[1], "'"), "'");
        }
        if (empty($array['carrier'])) return $this->returnEcho('02', '手机号错误');
        return  $this->returnEcho('00', '成功', $array);
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