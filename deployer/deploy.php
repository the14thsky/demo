<?php
/**
 * Git Deployment Script by Resonance
 *
 * Used for automatically deploying websites.
 * version 1.0
 */
class GitDeployScript
{
    private $git_baseDir = '/usr/local/cpanel/3rdparty/bin/';
    public function run_update(){
        $output = '';
        $showRemote = $this->outputScript('git remote -v');
        if(preg_match('/origin/',$showRemote)){
            $output .= $showRemote;
            $output .= $this->outputScript('git pull origin master');
        }
        $output .= $this->outputScript('git status');
        if(preg_match('/(modified:|new file:|deleted:|Untracked files:)/',$output)){
            date_default_timezone_set('Asia/Singapore');
            $output .= $this->outputScript('git add '.'view/');
            $output .= $this->outputScript('git commit -m "Auto updated by web-hook at '.date("j M Y, g:i:s a").'"');
            if(preg_match('/origin/',$showRemote)){
                $output .= $this->outputScript('git push origin master');
                $output .= 'Commits push to remote - done';
            }
        }
        return $output;
    }
    public function run_clone($command){
        return $this->outputScript($command);
    }
    public function run_status(){
        return $this->outputScript('git status');
    }
    public function run_custom_script($dir){
        $output = '';
        $showRemote = $this->outputScript('git remote -v');
        if(preg_match('/origin/',$showRemote)){
            $output .= $showRemote;
            $output .= $this->outputScript('git pull origin master');
        }
        $output .= $this->outputScript('git status');
        if(preg_match('/(modified:|new file:|deleted:|Untracked files:)/',$output)){
            date_default_timezone_set('Asia/Singapore');
            $output .= $this->outputScript('git add '.$dir);
            $output .= $this->outputScript('git commit -m "Auto updated by web-hook at '.date("j M Y, g:i:s a").'"');
            if(preg_match('/origin/',$showRemote)){
                $output .= $this->outputScript('git push origin master');
                $output .= 'Commits push to remote - done';
            }
        }
        return $output;
    }
    private function outputScript($command){
        $tmp = shell_exec($this->git_baseDir.$command);
        if($tmp != NULL) {
            $output = "<span style=\"color: #1D8324;\">\$</span> <span style=\"color: #729FCF;\">{$command}</span>\n";
            $output .= htmlentities(trim($tmp)) . "\n\n";
        }else{
            $output = "<span style=\"color: #1D8324;\">\$</span> <span style=\"color: #729FCF;\">{$command}</span>\n";
        }
        return $output;
    }
}
$git = new GitDeployScript();
$output = $git->run_update();
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>Git Deployment Script</title>
    <style type="text/css">
        body {background-color: #FFFFFF; color: #111111; padding: 0 2%;}
        h1 {font-family: "Imprint MT Shadow", sans-serif;font-weight:normal;}
    </style>
</head>
<body>
<h1>RESONANCE</h1>
<pre>
<h2>Git Deployment Script</h2>
    <?php echo $output; ?>
</pre>
</body>