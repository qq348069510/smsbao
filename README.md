# 短信宝快捷工具 v1.0.1

#### 介绍
适用于短信宝API v1 快捷工具 短信宝：http://www.smsbao.com/

#### 使用说明

1.  引入Smsbao.php文件，并引用类 `use smsbao\Smsbao;`
2.  初始化工具对象 `$smsbao = new Smsbao("短信宝平台用户名", "短信宝平台密码");`
3.  如果需要重新设置账号密码可使用 `$smsbao->setUser("短信宝平台用户名", "短信宝平台密码");`
4.  发送短信 `$smsbao->send("手机号","短信内容",发送类型);` 发送类型参数可省略，返回true为发送成功，false则是发送失败
5.  获取错误信息 `$smsbao->getErrorMessage();` 用于获取上一次失败后的错误信息
6.  提供三种发送类型（调用方式为类常量） `Smsbao::GN_SMS`：国内短信，`Smsbao::GW_SMS`：国外短信，`Smsbao::VOICE`：语音验证码，缺省值默认为国内短信
7.  查询账户余额 `$smsbao->query();` 返回内容为 [<em>发送条数：x条，剩余条数：x条</em>]。如果查询失败直接返回错误信息
8.  可设置是否使用SSL安全协议的安全接口请求 `$smsbao->setIsSSL(true)` true为使用SSL安全协议，false则不使用
9.  随机生成字符串助手函数，可快速生成验证码 `$smsbao->random(6,true)` 即生成长度为6位的数字验证码

#### Composer
##### 安装
    composer require qq348069510/smsbao -vvv

##### 更新
    composer update qq348069510/smsbao -vvv

##### 删除
    composer remove qq348069510/smsbao -vvv

#### 注意事项
为了避免被滥用，demo.php文件请在测试后删除

#### 版本日志
##### v1.0.1
      修复对php7.4的不兼容性
##### v1.0.0
      支持短信宝的发送国内外短信，语音验证码，账户余额查询功能
      支持设置是否使用SSL安全协议的安全接口
      支持随机字符串生成函数

#### 参与贡献

1.  短信宝官网开发文档(http://www.smsbao.com/openapi/)