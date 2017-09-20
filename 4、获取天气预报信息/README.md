基于PHP5.4 获取天气预报信息DEMO
===============================================
小黄牛
-----------------------------------------------

### 1731223728@qq.com 


+ 命令行工具当前版本 - V1.0.0.1

+ 上传日期 - 2017-9-20 11:14:00

+ 作者 - 小黄牛

+ 邮箱 - 1731223728@qq.com                                                                                                                    

+ 本接口是截取的新浪API



## 使用DEMO如下：


```
require 'vendor/Api.php';

# 使用DEMO
$obj = new Weather();
$num = $obj->Obtain('广州', 0);
echo '<pre>';
var_dump( $num );
````


### $this->Obtain()的参数说明：


``` 
$obj->Obtain(市名称, 查询类型);
查询类型主要有以下五种：
0 ：当天天气
1 ：第二天
2 ：第三天
3 ：第四天
4 ：第五天
```


### 返回值说明：


``` 
下面我将建立一个表格来列出这些对应的标签的说明（可能有误，个人分析结果）

标签	说明
city	对应的查询城市
status1	白天天气情况
status2	夜间天气情况
figure1	白天天气情况拼音
figure2	夜间天气情况拼音
direction1	白天风向
direction2	夜晚风向
power1	白天风力
power2	夜间风力
temperature1	白天温度
temperature2	夜间温度
ssd	体感指数
tgd1	白天体感温度
tgd2	夜间体感温度
zwx	紫外线强度
ktk	空调指数
pollution	污染指数
xcz	洗车指数
zho	综合指数？这个我不确定
diy	没猜出来是什么指数，没有数值
fas	同上
chy	穿衣指数
zho_shuoming	zho的说明，然而zho是什么指数我也不确定
diy_shuoming	同上
fas_shuoming	同上
chy_shuoming	穿衣指数说明
pollution_l	污染程度
zwx_l	紫外线指数概述
ssd_l	体感指数概述
fas_l	这个不知道
zho_l	这个也不清楚
chy_l	穿衣指数概述（可理解为穿衣建议）
ktk_l	空调指数概述
xcz_l	洗车指数概述
diy_l	这个不知道
pollution_s	污染指数详细说明
zwx_s	紫外线详细说明
ssd_s	体感详细说明
ktk_s	空调指数详细说明
xcz_s	洗车详细说明
gm	感冒指数
gm_l	感冒指数概述
gm_s	感冒指数详细说明
yd	运动指数
yd_l	运动指数概述
yd_s	运动指数详细说明
savedate_weather	天气数据日期
savedate_life	生活数据日期
savedate_zhishu	指数数据日期
udatetime	更新时间
```