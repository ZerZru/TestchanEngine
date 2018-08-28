<?php
    $WARNING = '
    Этот движок использует лицензию TestchanLicense 1.0(TCHL 1.0). Подробнее о настройках движка вы можете прочитать в PDF-файле "Настройки TestchanEngine"';
    $E_NAME = 'TestchanEngine';
    $VERSION = '1.0.0';
    $PHP_VERSION = 'PHP5';
    $BUILD = 'TestchanEngine v1.0.0 PHP5-0001a';
    $LICENSE = 'TCHL 1.0';
    $LICENSE_DIRNAME = 'http://scgofficial.esy.es/testchanengine/LICENSE.TXT';
    $SITE = 'http://scgofficial.esy.es/testchanengine/';

    $conf = parse_ini_file('config.ini');

    if ($conf['engine_language'] == 'russian') {
        if ($conf['show_engine_errors'] == 'true') {
            $engine_user = $conf['username'];
            $engine_host = $conf['host'];
            $engine_addres = $conf['site_addres'];
            
            if($conf['show_engine_build'] == 'true') {
                echo $BUILD.'<br>';
            } else if($conf['show_engine_build'] == 'false') {
                echo '';
            } else {
                echo '<strong>ShowEngineError</strong>: неизвестное значение в файле config.ini, show case, строка 12<br>';
            };
            
            if ($conf['show_engine_info'] == 'true') {
                echo '<script>
                        console.log("Название движка: '.$E_NAME.'; Версия: '.$VERSION.'; Билд: '.$BUILD.'; Лицензия: '.$LICENSE.'; Сайт: '.$SITE.'");
                        console.log("Пользователь: '.$engine_user.'; Хост: '.$engine_host.'; Адрес: '.$engine_addres.';");
                      </script>';
            } else if($conf['show_engine_info'] == 'false') {
                echo '';
            } else {
                echo '<strong>ShowEngineError</strong>: неизвестное значение в файле config.ini, show case, строка 13<br>';
            };
            
        } else if($conf['show_engine_errors'] == 'false') {
            $engine_user = $conf['username'];
            $engine_host = $conf['host'];
            $engine_addres = $conf['site_addres'];
            
            if($conf['show_engine_build'] == 'true') {
                echo $BUILD.'<br>';
            } else if($conf['show_engine_build'] == 'false') {
                echo '';
            };
            
            if ($conf['show_engine_info'] == 'true') {
                echo '<script>
                        console.log("Engine name: '.$E_NAME.'; Engine version: '.$VERSION.'; Engine build: '.$BUILD.'; Engine license: '.$LICENSE.'; Engine site: '.$SITE.'");
                        console.log("User: '.$engine_user.'; Host: '.$engine_host.'; Addres: '.$engine_addres.';");
                      </script>';
            } else if($conf['show_engine_info'] == 'false') {
                echo '';
            };
        }
    } else if ($conf['engine_language'] == 'english') {
        if($conf['show_engine_errors'] == 'true') {
            $engine_user = $conf['username'];
            $engine_host = $conf['host'];
            $engine_addres = $conf['site_addres'];
            
            if($conf['show_engine_build'] == 'true') {
                echo $BUILD.'<br>';
            } else if($conf['show_engine_build'] == 'false') {
                echo '';
            } else {
                echo '<strong>ShowEngineError</strong>: unknown value at config.ini file, show case, str 12<br>';
            };
            
            if ($conf['show_engine_info'] == 'true') {
                echo '<script>
                        console.log("Engine name: '.$E_NAME.'; Engine version: '.$VERSION.'; Engine build: '.$BUILD.'; Engine license: '.$LICENSE.'; Engine site: '.$SITE.'");
                        console.log("User: '.$engine_user.'; Host: '.$engine_host.'; Addres: '.$engine_addres.';");
                      </script>';
            } else if($conf['show_engine_info'] == 'false') {
                echo '';
            } else {
                echo '<strong>ShowEngineError</strong>: unknown value at config.ini file, show case, str 13<br>';
            };
        } else if($conf['show_engine_errors'] == 'false') {
            echo '';
        } else {
            echo '<strong>FatalEngineError</strong>: unknown value at config.ini file, show case, str 14<br>';
        };
    } else if($conf['engine_language'] == 'japanese') {
        echo '<strong>Special</strong>: 日本語は一時的に利用できません。ごめんなさい。';
    } else {
        echo '<strong>EngineLanguageError</strong>: unknown language at config.ini file, info case, str 3<br>
              <strong>EngineLanguageError</strong>: неизвестное значение в файле config.ini, info case, строка 3';
    };

    function start_job($forum) {
        #echo '<script>alert("Движок работает");</script>';
        $conf = parse_ini_file('config.ini');
        require_once dirname(__FILE__).'\conn.php';
        require_once 'connection.php';
        
        $counter = mysql_query('SELECT COUNT(`id`) FROM `treds`');
        $counter = mysql_fetch_array($counter);
        $pages = intval( ($counter[0] - 1) / $conf['pp']) + 1;

        if (isset($_GET['page'])) {
            $page = (int) $_GET['page'];
                if ($page > 0 && $page <= $pages) {
                    $start = $page * $conf['pp'] - $conf['pp'];
                    $sql = "SELECT * FROM `treds` WHERE `forum` = '".$forum."' ORDER BY `id` DESC LIMIT {$start},{$conf['pp']}";
                } else { 
                    $sql = 'SELECT * FROM `treds` WHERE `forum` = "'.$forum.'" ORDER BY `id` DESC LIMIT '.$conf['pp'];
                    $page = 1;
                }
        } else {
            $sql = 'SELECT * FROM `treds` WHERE `forum` = "'.$forum.'" ORDER BY `id` DESC LIMIT '.$conf['pp'];
            $page = 1;
        };
        
        $otvet = mysql_query($sql);

        date_default_timezone_set('Etc/GMT-3');
        $date = date('m/d/Y h:i:s', time());

        function getUserIP() {
            $client = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote = $_SERVER['REMOTE_ADDR'];

            if(filter_var($client, FILTER_VALIDATE_IP)) {
                $ip = $client;
            }
            elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
                $ip = $forward;
            }
            else {
                $ip = $remote;
            }

            return $ip;
        }

        $link = mysqli_connect($host, $user, $password, $database) or die("".mysqli_error($link));

        $user_ip = getUserIP();

        if(isset($_POST['name'])) {
            $ava = $_FILES['upload'];
            $ava_n = dirname(__FILE__).'/'.basename($ava['name']);

            #if(!is_uploaded_file($ava['tmp_name']) || !move_uploaded_file($ava['tmp_name'], $ava_n)){ die(); }
            $sql = mysqli_query($link, "INSERT INTO `treds` (`forum`, `name`, `theme`, `post`, `picture`, `date`, `ip`) VALUES ('".$forum."', '".$_POST['name']."', '".$_POST['theme']."', '".$_POST['message']."', '".$ava['name']."', '".$date."', '".$user_ip."')") or die("Такая тема уже существует или произошла неизвестная ошибка"/*.mysqli_error($sql)*/);
        }
        
        while($row = mysql_fetch_assoc($otvet)) {
            echo "<div class='tred'>
                    <span class='title'>{$row['theme']}</span>
                    -
                    <span class='author'>{$row['name']}</span>
                    <span class='date'>{$row['date']}</span>
                    <span class='id'>№{$row['id']}</span>
                    <a id='toggleLink2' href='javascript:void(0);' onclick='viewdiv2();' data-text-show='[ Закрыть форму ]' data-text-hide='[ Ответ ]'>[ Ответ ]</a>
                    <div class='media'>
                        <img src='{$row['picture']}' width='100%'>
                    </div>
                    <p class='text'>
                        {$row['post']}
                    </p>
                    <div id='answer' style='display:none;'>
                        <form action='test.php'>
                            <strong>Ник</strong> <input type='text' name='answer_author'>
                        </form>
                </div><br><br><br><br>";
        };
        mysqli_close($link);
    };
?>