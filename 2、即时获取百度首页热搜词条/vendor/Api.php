<?php
/*
 +----------------------------------------------------------------------
 + Title        : 接口核心类 - 即时获取百度12条热搜词
 + Author       : 小黄牛
 + Version      : V1.0.0.1
 + Initial-Time : 2017-9-18
 + Last-time    : 这个文件最后修改的时间 + 修改人的名称
 + Desc         : 
 +----------------------------------------------------------------------
*/

class Hotsearch{
    
     /**
      * 获取热词
      * @return array 获取结果
      */
     public function Obtain(){
        $url = 'http://www.baidu.com/s?wd=%E7%83%AD%E8%AF%8D';
        $content  = file_get_contents($url);
        $data     = explode('<th class="opr-toplist-right">搜索指数</th>', $content);
        $html     = $data[1];
        
        $regex4   = '/title="(.*)"/i';  
        if(preg_match_all($regex4, $html, $array)){  
            $res = [];
            for ($i=0; $i < 30; $i++) { 
                $data = explode('="', $array[0][$i]);
                $res[$i]['title'] = str_replace('"', '', str_replace(' href', '', $data[1]));
                $res[$i]['href']  = 'https://www.baidu.com' . str_replace('"', '', $data[2]);
            }
            
            $num = explode('<td class="opr-toplist-right">', $html);
            unset($num[0]);
            foreach ($num as $k=>$v) {
                $txt = explode('<i class="', $v);
                $res[$k-1]['num'] = $txt[0];

                if (stripos($txt[1], 'c-icon-down') !== false) {
                    $res[$k-1]['sort'] = '2'; // 降
                }else{
                    $res[$k-1]['sort'] = '1'; // 升
                }
            }
            return $this->Return('00', '成功', $res); 
        }
        return $this->Return('01', '失败'); 
     }

     /**
      * 组合返回内容
      * @param string $code 状态码
      * @param mixed  $msg  返回说明
      * @param mixed  $data 返回内容
      */
     protected function Return($code , $msg, $data=''){
        return [
            'code' => "'{$code}'",
            'msg'  => $msg,
            'data' => $data,
        ];
     }
}

