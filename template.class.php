<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2016/1/17
 * Time: 13:55
 */
include_once "config.php";
include_once "functions.php";

class Template {
    private $templateDir, $compileDir;

    function __construct($templateDir = '/templates/', $compileDir = '/templates_c/') {
        $this->templateDir = SITE_ROOT . $templateDir;

        if(!is_dir($this->templateDir)) {
            if(!mkdir($this->templateDir, 0644, true)) {
                log_and_jump(__FILE__, __LINE__, "创建模板目录失败", "errorpages/500.html");
                exit;
            }
        }

        $this->compileDir = SITE_ROOT . $compileDir;

        if(!is_dir($this->compileDir)) {
            if(!mkdir($this->compileDir, 0664, true)) {
                log_and_jump(__FILE__, __LINE__, "创建模板编译目录失败", "errorpages/500.html");
                exit;
            }
        }
    }

    function display($fileName) {
        $tplFile = $this->templateDir . $fileName;
        if(!file_exists($tplFile)) {
            log_and_jump(__FILE__, __LINE__, "模版文件：{$tplFile}不存在！", "errorpages/500.html");
            exit;
        }

        $fileName = str_replace('/', '_', $fileName);
        $comFile = $this->compileDir . "com_" . basename($fileName, '.tpl') . '.php';

        $expire = false;
        file_exists($comFile) && filemtime($comFile) < filemtime($tplFile) && !$expire && $expire = true;
        $content = file_get_contents($tplFile);
        $content = $this->replaceIncludeCheckExpire($content, $comFile, $expire);
        if($expire) {
            $content = $this->replaceOther($content);
            $handle = fopen($comFile, "w+");
            fwrite($handle, $content);
            fclose($handle);
        }

        include "$comFile";
    }

    private function replaceIncludeCheckExpire($content, $comFile, &$expire) {
        !file_exists($comFile) && !$expire && $expire = true;

        $pattern = '/<\{\s*include\s*"(.+?)"\s*\}>/i';
        if(preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach($matches as $match) {
                file_exists($comFile) && filemtime($comFile) < filemtime($match[1]) && !$expire && $expire = true;
                if($expire) {
                    break;
                }
            }

            $content = preg_replace_callback(
                $pattern,
                array($this, 'replaceInclude'),
                $content
            );
        }

        if(preg_match($pattern, $content)) {
            $content = $this->replaceIncludeCheckExpire($content, $comFile, $expire);
        }

        return $content;
    }

    private function replaceInclude($matches) {
        return file_get_contents($matches[1]);
    }

    private function replaceOther($content) {
        $pattern = array(
            '/<\{\s*echo\s*(.+?)\s*\}>/i',
            '/<\{\s*if\s*\((.+?)\)\s*\}>/i',
            '/<\{\s*else\s*if\((.+?)\)\s*\}>/i',
            '/<\{\s*else\s*\}>/i',
            '/<\{\s*\/if\s*\}>/i',
            '/<\{\s*(foreach|for)\s*\((.+?)\)\s*\}>/i',
            '/<\{\s*break\s*\}>/i',
            '/<\{\s*\/(foreach|for)\s*\}>/i',
        );
        $replace = array(
            '<?php echo $1; ?>',
            '<?php if($1) { ?>',
            '<?php } elseif($1) { ?>',
            '<?php } else { ?>',
            '<?php } ?>',
            '<?php $1($2) { ?>',
            '<?php break; ?>',
            '<?php } ?>',
        );
        $content = preg_replace($pattern, $replace, $content);

        if(preg_match('/<\{(.+?)\}>/i', $content)) {
            $content = $this->replaceOther($content);
        }

        return $content;
    }
}
