<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @package    gitFlic
 * @author     gmacleod
 * @copyright  (c) 2010 7cubed
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class API_Controller extends Standard_Controller {

	const ALLOW_PRODUCTION = TRUE;

	public function index() {
	  if (!IN_PRODUCTION) {
		  $profiler = new Profiler;
		}

    $this->add_js_foot_file('js/jquery-1.4.min.js');

		$content = new View('home_content');

    $this->render_markdown_template($content);
	}

  public function welcome() {
    $this->add_js_foot_file('js/jquery-1.4.min.js');

		$content = new View('home_content');

    $this->render_markdown_template($content);
  }

  public function tasklist() {
    $taskList = Array();

    echo json_encode($this->get_task_list());
  }

  public function task() {
    $taskId = $_REQUEST['task_id']; 

    echo json_encode($this->get_task_info($taskId));
  }

  public function upload() {
    $task_id = $_REQUEST['task_id'];
    $file_binary =  $_REQUEST['binary'];
    $file_type = $_REQUEST['file_type'];

    $stream_object_id = $this->insert_stream_object(
      $task_id, "IMAGE", "a short descriptive note"
    );
    
    // /storage/taskid/streamid.filename
    //$repo_root = "/home/gareth/73/trunk/DaPTtest/";
    $dir_path = $this->repo_root . "/" . $task_id;
    $file_path = $dir_path . "/" . $stream_object_id . "." . $file_type;

    // create the directory if it doesn't exist
    if (!file_exists($dir_path)) mkdir($dir_path);
    $dir = opendir($dir_path);

    echo $dir_path;

    $f = fopen($file_path, 'w');
    fwrite($f, $file_binary);
    fclose($f);
  }

  public function commit() {
    // task_id, stream_id
    
    if (commit_image(
        "/home/gareth/73/trunk/DaPTtest/1/12.png", $this->repo_root)) {
      echo "YES";
      return;
    }
    echo "NO";
  }

  public function taskstream() {
    $taskId = $_REQUEST['task_id'];

    echo json_encode($this->get_task_stream($taskId));
  }

  public function createtask() {
    // this needs to be a post
    $name = $_REQUEST['name'];
    $description = $_REQUEST['description'];

    echo json_encode($this->create_task($name, $description));
  }
}
