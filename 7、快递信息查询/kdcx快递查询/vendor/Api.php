<?php
/*
 +----------------------------------------------------------------------
 + Title        : 接口核心类 - 快递查询
 + Author       : 小黄牛
 + Version      : V1.0.0.1
 + Initial-Time : 2017-9-20
 + Last-time    : 2017-9-21 + 小黄牛
 + Desc         : 
 +----------------------------------------------------------------------
*/

class Express{

     /**
      * 执行查询
      * @param string $order 快递单号
      * @param string $type  快递类型，为空将自动识别
      * @return array 查询结果
      */
    public function Obtain($order='', $type=''){
        if (empty($order)) return $this->returnEcho('01', '快递单号不能为空');

        # 自动识别快递类型
        if (empty($type)) {
            $res  = json_decode($this->https_request('http://www.kdcx.cn/index.php?r=site/info&number='.$order), true);
            $type = $res[0]['exname'];
        }else{
            $type = $this->typeGet($type);
            if (!$type) return $this->returnEcho('02', '暂无该快递公司类型');
        }
        
        $res = json_decode($this->https_request('http://www.kdcx.cn/index.php?r=site/query&nu='.$order.'&exname='.$type), true);
        if (!$res || !$res['data']) return $this->returnEcho('03', '查询失败');

        return $this->returnEcho('00', '成功', $res['data']);
    }


    /**
      * 快递类型获取
      * @param string $type  快递类型
      * @return string 对应编码
      */
    protected function typeGet($type){
        $name = '';
        switch ($type){
            case '安捷快递': $name = 'anjie'; break;  
            case '长宇物流': $name = 'changyuwuliu'; break;
            case '大田物流': $name = 'datianwuliu'; break;  
            case '凤凰快递': $name = 'fenghuangkuaidi'; break;
            case '佳怡物流': $name = 'jiayiwuliu'; break;  
            case '京广速递': $name = 'jinguangsudikuaijian'; break;
            case '佳吉物流': $name = 'jjwl'; break;  
            case '晋越快递': $name = 'jykd'; break;
            case '快捷速递': $name = 'kuaijiesudi'; break;  
            case '联邦快递': $name = 'lianb'; break;
            case '联昊通物流': $name = 'lianhaowuliu'; break;  
            case '联昊通': $name = 'lianhaowuliu'; break;
            case '龙邦物流': $name = 'longbanwuliu'; break;  
            case '立即送': $name = 'lijisong'; break;
            case '乐捷递': $name = 'lejiedi'; break;  
            /** 以下为常用快递 **/
            case '': $name = ''; break;
            case '国通快递': $name = 'guotongkuaidi'; break;  
            case '汇通快递': $name = 'huitongkuaidi'; break;
            case '申通快递': $name = 'shentong'; break;  
            case '顺丰快递': $name = 'shunfeng'; break;
            case '天天快递': $name = 'tiantian'; break;  
            case '天地华宇': $name = 'tiandihuayu'; break;
            case '新邦物流': $name = 'xinbangwuliu'; break;  
            case '优速物流': $name = 'youshuwuliu'; break;
            case '圆通快递': $name = 'yuantong'; break;  
            case '韵达快递': $name = 'yunda'; break;
            case '中通快递': $name = 'zhongtong'; break;  
            case '急宅送': $name = 'zhaijisong'; break;
            case '芝麻开门': $name = 'zhimakaimen'; break;
            case '德邦物流': $name = 'debangwuliu'; break;
            case '德邦快递': $name = 'debangwuliu'; break;
            case 'ems'    : $name = 'ems'; break;  
            case '全峰快递': $name = 'quanfengkuaidi'; break;
            default: return false;
        }
        return $name;
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
     * 生成随机IP段
     * @return string IP地址
     */
    protected function randIp(){
        $ip_long = [
            ['607649792', '608174079'], //36.56.0.0-36.63.255.255
            ['975044608', '977272831'], //58.30.0.0-58.63.255.255
            ['999751680', '999784447'], //59.151.0.0-59.151.127.255
            ['1019346944', '1019478015'], //60.194.0.0-60.195.255.255
            ['1038614528', '1039007743'], //61.232.0.0-61.237.255.255
            ['1783627776', '1784676351'], //106.80.0.0-106.95.255.255
            ['1947009024', '1947074559'], //116.13.0.0-116.13.255.255
            ['1987051520', '1988034559'], //118.112.0.0-118.126.255.255
            ['2035023872', '2035154943'], //121.76.0.0-121.77.255.255
            ['2078801920', '2079064063'], //123.232.0.0-123.235.255.255
            ['-1950089216', '-1948778497'], //139.196.0.0-139.215.255.255
            ['-1425539072', '-1425014785'], //171.8.0.0-171.15.255.255
            ['-1236271104', '-1235419137'], //182.80.0.0-182.92.255.255
            ['-770113536', '-768606209'], //210.25.0.0-210.47.255.255
            ['-569376768', '-564133889'], //222.16.0.0-222.95.255.255
        ];
        $rand_key   = mt_rand(0, 14);
        $huoduan_ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
        return $huoduan_ip;
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
        $ip     = $this->randIp();
        $header = ['CLIENT-IP:'.$ip, 'X-FORWARDED-FOR:'.$ip];
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);		
		$response = curl_exec($curl);                       // 执行一个cURL会话并且获取相关回复
		curl_close($curl);                                  // 释放cURL句柄,关闭一个cURL会话
		
		return $response;
	}
}