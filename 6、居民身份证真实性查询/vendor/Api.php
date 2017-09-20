<?php
/*
 +----------------------------------------------------------------------
 + Title        : 接口核心类 - 居民身份证真实性查询
 + Author       : 小黄牛
 + Version      : V1.0.0.1
 + Initial-Time : 2017-9-20
 + Last-time    : 这个文件最后修改的时间 + 修改人的名称
 + Desc         : 
 +----------------------------------------------------------------------
*/

class RealIDcard{
     /**
      * 执行查询
      * @param string $id_card 身份证号
      * @return array 查询结果
      */
     public function Obtain($id_card=''){
        if (empty($id_card))            return $this->returnEcho('01', '身份证不允许为空');
        if (!$this->cardeVif($id_card)) return $this->returnEcho('02', '身份证错误');
        $data   = [];
        $html   = $this->https_request('http://idcard.911cha.com/', ['q'=>$id_card]);
        $array  = explode('<p class="l200">', $html); 
        $array  = explode('<p class="f12">', $array[1]);
        $top    = $array[0];
        $top    = str_replace(['p', 'br', 'san', '发证地：', '< class="red">', '< />', '生　日：', '性　别：'], ['','','','','','','',''], $top);
        $arr    = explode('< class="green">', $top);
        $data['address'] = $arr[1];
        $data['date']    = $arr[2];
        $data['sex']     = $arr[3];
        $array           = explode('<div class="otitle">', $array[1]);
        $data['msg']     = str_replace(['p' ,'因发证地中的红字地区已被撤并，'], ['',''], $array[0]);
        return $this->returnEcho('00', '成功', $data);
     }

     /**
      * 身份证合法性验证
      * @param string $id_card 身份证号
      * @return boole
      */
     protected function cardeVif($id_card){ 
        $id = strtoupper($id_card); 
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/"; 
        $arr_split = array(); 
        if (!preg_match($regx, $id)) return false;
        # 检查15位 
        if (15 == strlen($id)) { 
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/"; 
            @preg_match($regx, $id, $arr_split); 

            # 检查生日日期是否正确 
            $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4]; 
            if(!strtotime($dtm_birth)) return false;
            return true;
        # 检查18位 
        } else { 
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/"; 
            @preg_match($regx, $id, $arr_split); 
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4]; 

            # 检查生日日期是否正确 
            if (!strtotime($dtm_birth)) return false;

            # 检验18位身份证的校验码是否正确。 
            # 校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。 
      
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); 
            $arr_ch  = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); 
            $sign    = 0; 
            for ( $i = 0; $i < 17; $i++ ) { 
                $b = (int) $id{$i}; 
                $w = $arr_int[$i]; 
                $sign += $b * $w; 
            } 
            $n       = $sign % 11; 
            $val_num = $arr_ch[$n]; 
            if ($val_num != substr($id,17, 1)) return false;
            return true;
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