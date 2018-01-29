
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
    5.URL生成
        1.我们还可以使用系统提供的助手函数url来简化
            url('blog/read', 'name=thinkphp');
            // 等效于
            Url::build('blog/read', 'name=thinkphp');
        2.通常在模板文件中输出的话，可以使用助手函数，例如：
                {:url('blog/read', 'name=thinkphp')}
        3.注意：5.1版本，你需要引入think\facade\Url才能使用静态方法调用，其它用法不变。
三：请求和响应
    1.请求对象：
        1.传统方式调用
            该用法主要是用来告诉大家Request对象是如何实例化的，但实际开发中很少选择这种方式调用。
            namespace app\index\controller;
            use think\Request;
            class Index
            {
            public function hello($name = 'World')
            {
            $request = Request::instance();
            // 获取当前URL地址 不含域名
            echo 'url: ' . $request->url() . '<br/>';
            return 'Hello,' . $name . '！';
            }
            }
            访问下面的URL地址：
            http://tp5.com/index/index/hello.html?name=thinkphp
            页面输出结果为：
            url: /index/index/hello.html?name=thinkphp
            Hello,thinkphp！
    2.继承think\Controller
            如果控制器类继承了think\Controller的话，可以做如下简化调用：
            namespace app\index\controller;
            use think\Controller;
            class Index extends Controller
            {
                public function hello($name = 'World')
                {
                    // 获取当前URL地址 不含域名
                    echo 'url: ' . $this->request->url() . '<br/>';
                    return 'Hello,' . $name . '！';
                }
            }
    3.注意:【 5.1 】使用须知
            5.1的请求对象使用注意事项如下：
            不需要使用Request::instance()然后再调用方法，直接使用Facade特性即可，例如：
            namespace app\index\controller;
            use think\facade\Request;
            class Index
            {
                public function hello($name = 'World')
                {
                    // 获取当前URL地址 不含域名
                    echo 'url: ' . Request::url() . '<br/>';
                    return 'Hello,' . $name . '！';
                }
            }
    4.请求信息:
        1.系统推荐使用param方法统一获取当前请求变量:
            use think\Request;
            public function hello(Request $request)
            {
            echo '请求参数：';
            dump($request->param());
            echo 'name:'.$request->param('name');
            }
        2.系统提供了一个input助手函数来简化Request对象的param方法，用法如下：
            dump(input());
            echo 'name:'.input('name');
        3.可以设置全局的过滤方法，如下：
            // 设置默认的全局过滤规则 多个用数组或者逗号分隔
            'default_filter' => 'htmlspecialchars ',
        4.除了Param方法之外，Request对象也可以用于获取其它的输入参数，例如：
            namespace app\index\controller;
            use think\Request;
            class Index
            {
                public function hello(Request $request)
                {
                    echo 'GET参数：';
                    dump($request->get());
                    echo 'GET参数：name';
                    dump($request->get('name'));
                    echo 'POST参数：name';
                    dump($request->post('name'));
                    echo 'cookie参数：name';
                    dump($request->cookie('name'));
                    echo '上传文件信息：image';
                    dump($request->file('image'));
                }
            }
        5.使用助手函数的代码为：
            namespace app\index\controller;
            class Index
            {
                public function hello()
                {
                    echo 'GET参数：';
                    dump(input('get.'));
                    echo 'GET参数：name';
                    dump(input('get.name'));
                    echo 'POST参数：name';
                    dump(input('post.name'));
                    echo 'cookie参数：name';
                    dump(input('cookie.name'));
                    echo '上传文件信息：image';
                    dump(input('file.image'));
                }
            }
        6.获取请求参数
            把Hello方法改为如下：
            namespace app\index\controller;
            use think\Request;
            class Index
            {
                public function hello(Request $request)
                {
                    echo '请求方法：' . $request->method() . '<br/>';
                    echo '资源类型：' . $request->type() . '<br/>';
                    echo '访问IP：' . $request->ip() . '<br/>';
                    echo '是否AJax请求：' . var_export($request->isAjax(), true) . '<br/>';
                    echo '请求参数：';
                    dump($request->param());
                    echo '请求参数：仅包含name';
                    dump($request->only(['name']));
                    echo '请求参数：排除name';
                    dump($request->except(['name']));
                }
            }
            7.URL请求和信息方法可以总结如下：
                方法	作用
                domain	获取当前的域名
                url	获取当前的完整URL地址
                baseUrl	获取当前的URL地址，不含QUERY_STRING
                baseFile	获取当前的SCRIPT_NAME
                root	获取当前URL的root地址
                pathinfo	获取当前URL的pathinfo地址
                path	获取当前URL的pathinfo地址，不含后缀
                ext	获取当前URL的后缀
                type	获取当前请求的资源类型
                scheme	获取当前请求的scheme
                query	获取当前URL地址的QUERY_STRING
                host	获取当前URL的host地址
                port	获取当前URL的port号
                protocol	获取当前请求的SERVER_PROTOCOL
                remotePort	获取当前请求的REMOTE_PORT

                url、baseUrl、baseFile、root方法如果传入true，表示获取包含域名的地址。
            8.获取当前模块/控制器/操作信息
                hello方法修改如下：
                public function hello(Request $request, $name = 'World')
                {
                echo '模块：'.$request->module();
                echo '<br/>控制器：'.$request->controller();
                echo '<br/>操作：'.$request->action();
                }
        4.响应对象
            1.修改配置文件(默认为html)，添加：
            // 默认输出类型
            'default_return_type'    => 'json',
            2.默认的情况下发送的http状态码是200，如果需要返回其它的状态码，可以使用：
                return json($data, 201);
            3.  默认支持的输出类型包括：
                输出类型	快捷方法
                渲染模板输出	view
                JSON输出	json
                JSONP输出	jsonp
                XML输出	xml
                页面重定向	redirect
                所以，同样的可以使用xml方法输出XML数据类型：return xml($data, 201);
            4.成功失败提示并跳转:if ('thinkphp' == $name) {
                                $this->success('欢迎使用ThinkPHP
                                5.0','hello');
                                } else {
                                $this->error('错误的name','guest');
            5.跳转：(页面重定向):
                页面重定向
                如果要进行页面重定向跳转，可以使用：
                namespace app\index\controller;
                use \traits\controller\Jump;
                public function index($name='')
                {
                if ('thinkphp' == $name) {
                $this->redirect('http://thinkphp.cn');
                } else {
                $this->success('欢迎使用ThinkPHP','hello');
                }
                }
                public function hello()
                {
                return 'Hello,ThinkPHP!';
                }
            在任何时候（即使没有引入Jump trait的话），我们可以使用系统提供的助手函数redirect函数进行重定向。
                return redirect('http://thinkphp.cn');
            注意，使用redirect助手函数重定向的时候必须加上return返回才会生效。
四：数据库
    1.原生查询：原生查询
        设置好数据库连接信息后，我们就可以直接进行原生的SQL查询操作了，
        包括query和execute两个方法，分别用于查询操作和写操作，下面我们来实现数据表think_user的CURD操作。
        创建（create）
        // 插入记录
        $result = Db::execute('insert into think_data (id, name ,status) values (5, "thinkphp",1)');
        dump($result);
        更新（update）
        // 更新记录
        $result = Db::execute('update think_data set name = "framework" where id = 5 ');
        dump($result);
        读取（read）
        // 查询数据
        $result = Db::query('select * from think_data where id = 5');
        dump($result);
        query方法返回的结果是一个数据集（数组），如果没有查询到数据则返回空数组。
        删除（delete）
        // 删除数据
        $result = Db::execute('delete from think_data where id = 5 ');
        dump($result);
        注:query方法用于查询，默认情况下返回的是数据集（二维数组），execute方法的返回值是影响的记录数。

        $db1 = Db::connect('db1');
        $db2 = Db::connect('db2');
        $db1->query('select * from think_data where id = 1');
        $db2->query('select * from think_data where id = 1');
        Db::execute('insert into think_data (id, name ,status) values (?, ?, ?)', [8, 'thinkphp', 1]);
        $result = Db::query('select * from think_data where id = ?', [8]);
        dump($result);
    2.查询构造器：
        1.ThinkPHP 5.0查询构造器使用 PDO参数绑定，以保护应用程序免于 SQL注入，
            因此传入的参数不需额外转义特殊字符。
            // 插入记录
            Db::name('data')
            ->insert(['id' => 18, 'name' => 'thinkphp']);
            // 更新记录
            Db::name('data')
            ->where('id', 18)
            ->update(['name' => "framework"]);
            // 查询数据
            $list = Db::name('data')
            ->where('id', 18)
            ->select();
            dump($list);
            // 删除数据
            Db::name('data')
            ->where('id', 18)
            ->delete();
        2.如果使用系统提供的助手函数db则可以进一步简化查询代码如下：
            $db = db('data');
            // 插入记录
            $db->insert(['id' => 20, 'name' => 'thinkphp']);
            // 更新记录
            $db->where('id', 20)->update(['name' => "framework"]);
            // 查询数据
            $list = $db->where('id', 20)->select();
            dump($list);
            // 删除数据
            $db->where('id', 20)->delete();
            注意：db助手函数在V5.0.9之前版本默认会每次重新连接数据库，因此应当尽量避免多次调用。
    3.连式操作：支持链式操作的查询方法包括：
            方法名	描述
            select	查询数据集
            find	查询单个记录
            insert	插入记录
            update	更新记录
            delete	删除记录
            value	查询值
            column	查询列
            chunk	分块查询
            count等	聚合查询
    4.事务支持:
            注意：
            由于需要用到事务的功能，请先修改数据表的类型为InnoDB，而不是MyISAM。
            对于事务的支持，最简单的方法就是使用transaction方法，只需要把需要执行的事务操作封装到闭包里面即可自动完成事务，例如：
            Db::transaction(function () {
            Db::table('think_user')
            ->delete(1);
            Db::table('think_data')
            ->insert(['id' => 28, 'name' => 'thinkphp', 'status' => 1]);
            });
            一旦think_data表写入失败的话，系统会自动回滚，写入成功的话系统会自动提交当前事务。
            也可以手动控制事务的提交，上面的实现代码可以改成：
            // 启动事务
            Db::startTrans();
            try {
            Db::table('think_user')
            ->delete(1);
            Db::table('think_data')
            ->insert(['id' => 28, 'name' => 'thinkphp', 'status' => 1]);
            // 提交事务
            Db::commit();
            } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            }
            注意：
            事务操作只对支持事务的数据库，并且设置了数据表为事务类型才有效，
            在Mysql数据库中请设置表类型为InnoDB。并且事务操作必须使用同一个数据库连接。
五:查询语言:
    1.查询表达式:
        where('id', 1)....where('id', '=', 1)....where('id', 'in', [1, 2, 3])...where('id', 'between', [5, 8])
            ->where('id', 'between', [1, 3])
            // name 中包含think
            ->where('name', 'like', '%think%')
    2.批量查询:
        where([
        'id'   => ['between', '1,3'],
        'name' => ['like', '%think%'],
        ])

        ->where('name', 'like', '%think%')
        ->where('id', ['in', [1, 2, 3]], ['between', '5,8'], 'or')

        5.1的数组查询方式有所改变，必须使用下面的方式

        $result = Db::name('data')
        ->where([
        ['id', 'between', '1,3'],
        [ 'name', 'like', '%think%'],
        ])->select();
        dump($result);
    3.获取值和列：
        1.如果仅仅是需要获取某行表的某个值，可以使用value方法：->value('name');
        2.也支持获取某个列的数据，使用column方法，例如：->column('name');或者：->column('name', 'id');//以ID为索引
    4.分块查询：
    ->chunk(100, function ($list) {
        // 处理100条记录
        foreach($list as $data){

        }
    },'uid');//uid为排序字段,如果有主键可以不要
六.模型和关联
    1.基础操作:
            use app\index\model\User as UserModel;
            有一种方式可以让你省去别名定义，系统支持统一对控制器类添加Controller后缀，修改配置参数：
            // 是否启用控制器类后缀
            'controller_suffix'  => true,
        1.// 新增用户数据
            public function add()
            {
                $user           = new User;
                $user->nickname = '流年';
                $user->email    = 'thinkphp@qq.com';
                $user->birthday = strtotime('1977-03-05');
                if ($user->save()) {
                return '用户[ ' . $user->nickname . ':' . $user->id . ' ]新增成功';
                } else {
                return $user->getError();
                }
            }

                $user['nickname'] = '看云';
                $user['email']    = 'kancloud@qq.com';
                $user['birthday'] = strtotime('2015-04-02');
                if ($result = UserModel::create($user)) {
                return '用户[ ' . $result->nickname . ':' . $result->id . ' ]新增成功';
                } else {
                return '新增出错';
                }
        2.// 强制执行数据更新操作
            $user->isUpdate()->save();
        3.批量新增
            $user = new UserModel;
            $list = [
            ['nickname' => '张三', 'email' => 'zhanghsan@qq.com', 'birthday' => strtotime('1988-01-15')],
            ['nickname' => '李四', 'email' => 'lisi@qq.com', 'birthday' => strtotime('1990-09-19')],
            ];
            if ($user->saveAll($list)) {
            return '用户批量新增成功';
            } else {
            return $user->getError();
            }
        4.查询数据:$user = UserModel::get($id);
            模型的get方法用于获取数据表的数据并返回当前的模型对象实例，通常只需要传入主键作为参数，
            如果没有传入任何值的话，则表示获取第一条数据。
            在此提醒一点，如果你是在模型的内部获取数据，
            请不要使用$this->nickname，而应该使用$this->getAttr('nickname')方式替代

            通过用户的email来查询模型数据:$user = UserModel::getByEmail('thinkphp@qq.com');
            可以传入数组作为查询条件:$user = UserModel::get(['nickname'=>'流年']);
        5.数据列表:
            如果要查询多个数据，可以使用模型的all方法:$list = UserModel::all();
        6.更新数据:
            默认情况下，查询返回的模型实例如果执行save操作都是执行的数据库update（更新数据）操作，
            如果你需要实例化执行save执行数据库的insert（新增数据）操作，请确保在save方法之前调用isUpdate方法：
            // 强制进行数据新增操作
            $user->isUpdate(false)->save();

            $user['id']       = (int) $id;
            $user['nickname'] = '刘晨';
            $user['email']    = 'liu21st@gmail.com';
            UserModel::update($user);
            return '更新用户成功';
        7.删除数据：
            // 删除用户数据
            public function delete($id)
            {
                $user = UserModel::get($id);
                if ($user) {
                $user->delete();
                return '删除用户成功';
                } else {
                return '删除的用户不存在';
                }
            }

            // 删除用户数据
            public function delete($id)
            {
                $result = UserModel::destroy($id);
                if ($result) {
                return '删除用户成功';
                } else {
                return '删除的用户不存在';
                }
            }
    2.读取器和修改器：get + 属性名的驼峰命名+ Attr
                        set + 属性名的驼峰命名+ Attr
    3.查询范围:scope + 查询范围名称
            // status查询
            protected function scopeStatus($query)
            {
            $query->where('status', 1);
            }
    8.模型输出:输出数组:dump($user->toArray());//可以使用toArray方法把当前的模型对象输出为数组。
               指定属性:dump($user->visible(['id','nickname','email'])->toArray());
                dump($user->append(['user_status'])->toArray());
                输出JSON: return $user->toJson();
七、视图和模板
    1.模板输出:
        方法	描述
        assign	模板变量赋值
        fetch	渲染模板文件
        display	渲染内容
        engine	初始化模板引擎
        为了避免XSS攻击，5.1版本中的变量输出默认会使用htmlentities过滤输出，
        如果你确实需要输出HTML标签，则必须使用raw过滤
        {$user.content|raw}
    2.分页输出:
            可以很简单的输出用户的分页数据，控制器index方法修改为：
            // 获取用户数据列表
            public function index()
            {
            // 分页输出列表 每页显示3条数据
            $list = UserModel::paginate(3);
            $this->assign('list',$list);
            return $this->fetch();
            }
            模板文件修改为：

            <link rel="stylesheet" href="/static/bootstrap/css/bootstrap.min.css" />
            <h2>用户列表（{$list->total()}）</h2>
            {volist name="list" id="user"}
            ID：{$user.id}<br/>
            昵称：{$user.nickname}<br/>
            邮箱：{$user.email}<br/>
            生日：{$user.birthday}<br/>
            ------------------------<br/>
            {/volist}
            {$list->render()}

            如果是5.1的话，分页输出需要改为如下：
            {$list | raw}
    3.公共模板：
            公共模板
            加上之前定义的创建用户的模板，现在已经有两个模板文件了，为了避免重复定义模板，可以把模板的公共头部和尾部分离出来，便于维护。

            我们把模板文件拆分为三部分：

            application/index/view/user/header.html
            application/index/view/user/index.html
            application/index/view/user/footer.html
            header.html内容为：
            <!doctype html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>查看用户列表</title>
                <style>
                    body{
                        color: #333;
                        font: 16px Verdana, "Helvetica Neue", helvetica, Arial, 'Microsoft YaHei', sans-serif;
                        margin: 0px;
                        padding: 20px;
                    }

                    a{
                        color: #868686;
                        cursor: pointer;
                    }
                    a:hover{
                        text-decoration: underline;
                    }
                    h2{
                        color: #4288ce;
                        font-weight: 400;
                        padding: 6px 0;
                        margin: 6px 0 0;
                        font-size: 28px;
                        border-bottom: 1px solid #eee;
                    }
                    div{
                        margin:8px;
                    }
                    .info{
                        padding: 12px 0;
                        border-bottom: 1px solid #eee;
                    }

                    .copyright{
                        margin-top: 24px;
                        padding: 12px 0;
                        border-top: 1px solid #eee;
                    }
                </style>
            </head>
            <body>
            footer.html内容为：
            <div class="copyright">
                <a title="官方网站" href="http://www.thinkphp.cn">ThinkPHP</a>
                <span>V5</span>
                <span>{ 十年磨一剑-为API开发设计的高性能框架 }</span>
            </div>
            </body>
            </html>
            index.html内容为：
            {include file="user/header" /}
            <h2>用户列表（{$count}）</h2>
            {volist name="list" id="user" }
            <div class="info">
                ID：{$user.id}<br/>
                昵称：{$user.nickname}<br/>
                邮箱：{$user.email}<br/>
                生日：{$user.birthday}<br/>
            </div>
            {/volist}
            {include file="user/footer" /}
            公共头部模板文件中可能存在一些变量，例如这里的页面标题不同的页面会有不同，可以使用

            {include file="user/header" title="查看用户列表" /}
            然后把头部模板文件中的

            <title>查看用户列表</title>
            改为：

            <title>[title]</title>
    4.布局模板：
            {include file="user/header" /}
            {__CONTENT__}
            {include file="user/footer" /}
            application/index/view/user/index.html改成：
            {layout name="layout" /}
            <h2>用户列表（{$count}）</h2>
            {volist name="list" id="user" }
            <div class="info">
                ID：{$user.id}<br/>
                昵称：{$user.nickname}<br/>
                邮箱：{$user.email}<br/>
                生日：{$user.birthday}<br/>
            </div>
            {/volist}
    5.输出替换：5.1版本取消了视图输出替换功能，可以使用模板引擎的内容替换功能替代，
        直接在template.php配置文件中，设置如下：
            'tpl_replace_str' => [
            '__PUBLIC__'    =>  '/static',
            ],
    6.渲染内容:
        display方法用于渲染内容而不是模板文件输出，和直接使用echo输出的区别是display方法输出的内容支持
        模板标签的解析。
    7.助手函数
        可以使用系统提供的助手函数view简化模板渲染输出（注意不适用于内容渲染输出）：
        前面的模板渲染代码可以改为：
        namespace app\index\controller;

        use app\index\model\User as UserModel;

        class User
        {
            // 读取用户数据
            public function read($id='')
            {
                $user = UserModel::get($id);
                return view('', ['user' => $user]);
            }
        }




