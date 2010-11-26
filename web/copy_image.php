<?php

// pretty prints json
function json_format($json) 
{ 
    $tab = "  "; 
    $new_json = ""; 
    $indent_level = 0; 
    $in_string = false; 

    $json_obj = json_decode($json); 

    if($json_obj === false) 
        return false; 

    $json = json_encode($json_obj); 
    $len = strlen($json); 

    for($c = 0; $c < $len; $c++) 
    { 
        $char = $json[$c]; 
        switch($char) 
        { 
            case '{': 
            case '[': 
                if(!$in_string) 
                { 
                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1); 
                    $indent_level++; 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case '}': 
            case ']': 
                if(!$in_string) 
                { 
                    $indent_level--; 
                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char; 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case ',': 
                if(!$in_string) 
                { 
                    $new_json .= ",\n" . str_repeat($tab, $indent_level); 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case ':': 
                if(!$in_string) 
                { 
                    $new_json .= ": ";
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case '"': 
                if($c > 0 && $json[$c-1] != '\\') 
                { 
                    $in_string = !$in_string; 
                } 
            default: 
                $new_json .= $char; 
                break;                    
        } 
    } 

    return $new_json; 
} 

// given FULL path of image and FULL path of repo dir
// adds image to repo
// returns false if failed
function commit_image($image_path, $repo_path) {
    $cwd = getcwd();
    if (!chdir($repo_path)) {
        error_log("Faulty repo_path.");
        return false;
    }

    $temp_images_dir = 'TEMP_IMAGES';
    $editable_tag_file = '.gitflic';
    $backup_tag_file = '.gitflic.bak';

    // parse names
    $components = explode(DIRECTORY_SEPARATOR, $image_path);
    $comp_size = count($components);
    $task_id = $components[$comp_size-2];
    $base_name = $components[$comp_size-1];
    preg_match('/(.*?)\.(.*?)$/', $base_name, $matches);
    $image_index = $matches[1];
    $extension = $matches[2];

    $temp_image_path = $temp_images_dir . DIRECTORY_SEPARATOR . 
        "{$task_id}.{$extension}";

    $try_again = true;
    while ($try_again) {
        exec("git pull origin master", $output, $return);
        if ($return != 0) {
            error_log("Failed to pull from origin.");
            chdir($cwd);
            return false;
        }

        if (file_exists($editable_tag_file)) {
            $tag_objs = json_decode(file_get_contents($editable_tag_file));
        } else {
            $tag_objs = array();
        }

        $tag_obj = NULL;
        foreach ($tag_objs as $obj) {
            if ($obj->task_id == $task_id) {
                $tag_obj = $obj;
            }
        }

        if ($tag_obj != NULL) {
            foreach ($tag_obj->paths as $path) {
                exec(
                    "cp -f $image_path $path && git add $path",
                    $output, $return);
                if ($return != 0) {
                    error_log("Failed to copy to destination.");
                    chdir($cwd);
                    return false;
                }
            }
        } else {
            $tag_obj = array(
                "task_id" => $task_id,
                "paths" => array($temp_image_path)
            );
            $tag_objs [] = $tag_obj;

            $json = json_format(json_encode($tag_objs));
            file_put_contents($editable_tag_file, $json);
            file_put_contents($backup_tag_file, $json);

            exec("mkdir -p $temp_images_dir && " .
                "cp -f $image_path $temp_image_path && " .
                "git add $temp_image_path $editable_tag_file $backup_tag_file",
                $output, $return);
            if ($return != 0) {
                error_log("Failed to copy to destination.");
                chdir($cwd);
                return false;
            }
        }

        exec("git commit -m 'Updated image {$task_id}.'", $output, $return);
        /*  can't really test this because possibly nothing changed
        if ($return != 0) {
            error_log("Failed to commit. Reverting local changes.");
            exec("git reset --hard HEAD");
            chdir($cwd);
            return false;
        }
         */

        // pray to god there are no conflicts
        exec("git push origin master", $output, $return);

        if ($return != 0) {
            exec("git reset --hard HEAD^", $output, $return);
            error_log("Push failed; resetting and trying again.\n");
        } else {
            $try_again = false;
        }
    }

    chdir($cwd);
    return true;
}

/*
$repo_path = '/Users/gilbertl/Dropbox/Projects/testchild';
$image_path = '/Users/gilbertl/Dropbox/Projects/testimages/2342342/5.jpg';

commit_image($image_path, $repo_path);
 */


?>
