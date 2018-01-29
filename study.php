
一.基础
    1.入口文件
        a.// 定义应用目录
            define('APP_PATH', __DIR__ . 'application/');//修改入口文件配置
            // 加载框架引导文件
            require __DIR__ . 'thinkphp/start.php';
        b.//切换到命令行模式下，进入到应用根目录并执行如下指令：
        php think build --module demo
    2.调试模式
        // 关闭调试模式
        'app_debug' =>  false,
    3.命名空间
        app命名空间通常代表了文件的起始目录为application
        think命名空间则代表了文件的起始目录为thinkphp/library/think
        默认情况下（如果控制器名为驼峰写法：HelloWord）正确的方法是使用下面的URL进行访问:
        http://tp5.com/index.php/index/hello_world
        错误的写法：http://tp5.com/index.php/index/HelloWorld

        因为默认的URL访问是不区分大小写的，全部都会转换为小写的控制器名，
        除非你在应用配置文件中，设置了关闭url自动转换如下：
        'url_convert' => false,
        那么就可以正常访问
        http://tp5.com/index.php/index/HelloWorld

            继承：
            这里使用了use来导入一个命名空间的类库，然后可以在当前文件中直接使用该别名而不需要使用完整的命名空间路径访问类库。也就说，如果没有使用
            use think\Controller;
            就必须使用：
            class Index extends \think\Controller

            赋值并输出：
                $this->assign('name', $name);
                return $this->fetch();
二.URL和路由
    1.URL访问
        a.不支持普通模式访问(http://tp5.com/index.php?m=index&c=Index&a=hello)
        b.模块名都会强制小写。
    2.参数传入
        a.按照参数顺序获取  'url_param_type' => 1,
          :http://tp5.com/index.php/index/index/hello/thinkphp/shanghai(thinkphp为第一个参数，shanghai为第二个参数)
        b.不按照参数顺序传入：http://tp5.com/index.php/index/index/hello/name(参数名)/thinkphp(参数值)/city/shanghai
    3.隐藏入口
        a.以Apache为例，需要在入口文件的同级添加.htaccess文件（官方默认自带了该文件），内容如下：
        <IfModule mod_rewrite.c>
            Options +FollowSymlinks -Multiviews
            RewriteEngine on
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
        </IfModule>
        b.如果是Nginx环境的话，可以在Nginx.conf中添加：
            location / { // …..省略部分代码
            if (!-e $request_filename) {
            rewrite  ^(.*)$  /index.php?s=/$1  last;
            break;
            }
            }
    4.定义路由
        1.我们在路由定义文件（application/route.php）里面添加一些路由规则
            return [
            // 添加路由规则 路由到 index控制器的hello操作方法
            'hello/:name' => 'index/index/hello',
            ];
            该路由规则表示所有hello开头的并且带参数的访问都会路由到index控制器的hello操作方法。
            路由之前的URL访问地址为：
            http://tp5.com/index/index/hello/name/thinkphp
            定义路由后就只能访问下面的URL地址
            http://tp5.com/hello/thinkphp
            注意：
