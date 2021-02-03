<?php
/**
 * Created by PhpStorm.
 * User: steven
 * Date: 9/22/2017
 * Time: 4:13 PM
 */

class HTMLGenerator {
    public $input_folder = 'data/';
    public $output_folder = 'output/';
    public $output_type = '.html';

    function dir_to_array($dir) {
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array(".",".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $this->dir_to_array($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }
        krsort($result,SORT_STRING);
        return $result;
    }
    function show_dir_list3($dir_array){
        $parent_dir[0] = '';
        foreach($dir_array as $parent => $child) {
            $parent_dir[0] = $parent_dir[0].'';
            if(is_int($parent)){
                echo $parent_dir[0].$child.'<br>';
            }elseif(!is_int($parent)){
                foreach($child as $parent1 => $child1) {
                    $parent_dir[1] = $parent_dir[0].$parent.'/';
                    if(is_int($parent1)){
                        echo $parent_dir[1].$child1.'<br>';
                    }elseif(!is_int($parent1)){
                        foreach($child1 as $parent2 => $child2) {
                            $parent_dir[2] = $parent_dir[1].$parent1.'/';
                            if(is_int($parent2)){
                                echo $parent_dir[2].$child2.'<br>';
                            }elseif(!is_int($parent2)){
                                foreach($child1 as $parent3 => $child3) {
                                    $parent_dir[3] = $parent_dir[2].$parent2.'/';
                                    if(is_int($parent2)){
                                        echo $parent_dir[3].$child3.'<br>';
                                    }elseif(!is_int($parent2)){
                                        echo '';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    function show_dir_list2($dir_array){
        $count = 0;
        foreach($dir_array as $parent => $child) {
            if(is_array($child)){
                $directory[$count] = $parent.'/';
                foreach($child as $parent1 => $child1) {
                    if(is_array($child1)){
                        $directory[$count+1] = $directory[$count].$parent1.'/';
                        foreach($child1 as $parent2 => $child2) {
                            if(is_array($child2)){
                                $directory[$count+2] = $directory[$count+1].$parent2.'/';
                            }elseif(!is_array($child2)){
                                echo $directory[$count+1].$child2.'<br>';
                            }
                        }
                    }elseif(!is_array($child1)){
                        echo $directory[$count].$child1.'<br>';
                    }
                }
            }elseif(!is_array($child)){
                echo $child.'<br>';
            }
        }
    }
    function show_dir_list($dir_array,$directory=null,$count=0){
        foreach($dir_array as $parent => $child){
            if(is_array($child)){
                if($directory!=null){
                    if(count($directory)==$count){
                        $directory[$count] = $directory[$count-1].$parent.DIRECTORY_SEPARATOR;
                        $count++;
                    }
                }elseif($directory==null){
                    $directory[$count] = $parent.DIRECTORY_SEPARATOR;
                    $count++;
                }
                print_r($directory);
                echo '<br>';
                $this->show_dir_list($child,$directory,$count);
            }elseif(!is_array($child)){
                if($directory!=null){
                    echo $directory[$count-1].$child.'<br>';
                }else{
                    echo $child.'<br>';
                }
            }
        }
    }
    function dir_list($dir_array,$parent_dir=''){
        foreach($dir_array as $parent => $child){
            if(is_int($parent)){
                echo $parent_dir.$child.'<br>';
            }elseif(!is_int($parent)){
                $parent_dir = $parent.'/';
                $this->dir_list($child,$parent_dir);
            }
        }
    }
    function prepare_dir_list($dir_array){
        foreach($dir_array as $parent => $child){
            if(is_int($parent)){
                echo $child.'<br>';
            }elseif(!is_int($parent)){
                echo '<strong>'.$parent.'</strong>';
                echo '<div style="padding-left:20px;">';
                $this->prepare_dir_list($child);
                echo '</div>';
            }
        }
    }
    function prepare_dir_styled_list($dir_array,$output='',$count=0){
        $output .= '<ul>';
        $count++;
        foreach($dir_array as $parent => $child) {
            if(is_int($parent)){
                $output .= '<li><a href="">'.$child.'</a></li>';
                $count++;
            }elseif(!is_int($parent)){
                $count++;
                $output .= '<li><input type="checkbox" id="item-'.$count.'"><label for="item-'.$count.'">'.$parent.'</label>';
                $count++;
                $output = $this->prepare_dir_styled_list($child,$output,$count);
                $count++;
                $output .= '</li>';
            }
            $count++;
        }
        $output .= '</ul>';
        return $output;
    }
    function generate($filename,$file_destination){
        $file_destination = fopen($file_destination,'w+');
        chmod($file_destination,0777);

        ob_start();
        include($filename);
        $data = ob_get_contents();
        fwrite($file_destination,$data);
        fclose($file_destination);
        ob_end_clean();
    }
    function generateFile($fileIn,$newfile,$chmod=0777){
        $newfile = fopen($newfile,"w+");
        $chmod = chmod($newfile,$chmod);
        if(!$newfile) die('Error creating the file!');
        if(!$chmod) die('Error setting the file permissions');

        ob_start();
        include($fileIn);
        $data = ob_get_clean();
        fwrite($newfile,$data);
        fclose($newfile);
    }
    function show_directory_list(){
        echo '<div class="directory">';
        echo $this->prepare_dir_styled_list($this->dir_to_array($this->input_folder));
        echo '</div>';
    }
}
$htmlgen = new HTMLGenerator();

$htmlgen->show_directory_list();
echo '<br><br>';
$dir_array = $htmlgen->dir_to_array('data/');
print_r($dir_array);
echo '<br><br>';
$htmlgen->show_dir_list($dir_array);
?>
<style type="text/css">
    .directory ul,
    .directory li {
        padding: 0;
        margin: 0;
        list-style: none;
    }
    .directory input {
        position: absolute;
        opacity: 0;
    }
    .directory {
        font: normal 11px "Segoe UI", Arial, Sans-serif;
        -moz-user-select: none;
        -webkit-user-select: none;
        user-select: none;
    }
    .directory a {
        color: #00f;
        text-decoration: none;
    }
    .directory a:hover {
        text-decoration: underline;
    }
    .directory input + label + ul {
        margin: 0 0 0 22px;
    }
    .directory input ~ ul {
        display: none;
    }
    .directory label,
    .directory label::before {
        cursor: pointer;
    }
    .directory input:disabled + label {
        cursor: default;
        opacity: .6;
    }
    .directory input:checked:not(:disabled) ~ ul {
        display: block;
    }
    .directory label,
    .directory label::before {
        background: url("icons.png") no-repeat;
    }
    .directory label,
    .directory a,
    .directory label::before {
        display: inline-block;
        height: 16px;
        line-height: 16px;
        vertical-align: middle;
    }
    .directory label {
        background-position: 18px 0;
    }
    .directory label::before {
        content: "";
        width: 16px;
        margin: 0 22px 0 0;
        vertical-align: middle;
        background-position: 0 -32px;
    }
    .directory input:checked + label::before {
        background-position: 0 -16px;
    }
    @media screen and (-webkit-min-device-pixel-ratio:0) {
        .directory {
            -webkit-animation: webkit-adjacent-element-selector-bugfix infinite 1s;
        }
        @-webkit-keyframes webkit-adjacent-element-selector-bugfix {
            from { padding: 0; }
            to { padding: 0; }
        }
    }
</style>
