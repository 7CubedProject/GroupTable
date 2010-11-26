<?php defined('SYSPATH') OR die('No direct access allowed.');

define('TASK_TABLE', 'task');
define('TASK_STREAM_TABLE', 'task_stream');
define('STREAM_META_TABLE', 'stream_meta');

/**
 * @package    uwdata
 * @author     Jeff Verkoeyen
 * @copyright  (c) 2010 Jeff Verkoeyen
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class Standard_Controller extends Template_Controller {

  const ALLOW_PRODUCTION = FALSE;

  // Set the name of the template to use
  public $template = 'template';
  public $auto_render = FALSE;

  // TODO: change this!!!
  public $repo_root = "/home/gareth/73/trunk/DaPTtest/";

  public function __construct() {
    parent::__construct();

    $this->session = Session::instance();

    // Private: Use $this->get_db to access the db instance.
    $this->db = null;

    $this->template->js_foot_files = array();
    $this->template->css_files = array();
    $this->template->title = array('7Cubed App');
  }

  protected function get_db() {
    if (!$this->db) {
      $this->db = new SQLite3(APPPATH."cache/db.sqlite");
    }
    return $this->db;
  }

  private function get_all_results($result) {
    $rows = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $rows []= $row;
    }
    return $rows;
  }

  protected function get_venue_list($groupSize) {
    $result = $this->get_db()->query(
      "SELECT * FROM ". "venue" ." WHERE " . 
        sqlite_escape_string($groupSize) . " > 0 " . ";"
    );
    return $this->get_all_results($result);
  }

  /**
   * Gets the juicy details about the task.
   *
   * @return array(
   *           'title'          =>,
   *           'description'    =>,
   *           'is_completed'   =>, [true/false]
   *         )
   */
  protected function get_task_info($task_id) {
    $result = $this->get_db()->query(
      "SELECT task_title, task_description, is_completed FROM ".TASK_TABLE.
      " WHERE task_id = ".sqlite_escape_string($task_id).
      " LIMIT 1;"
    );

    return $result->fetchArray(SQLITE3_ASSOC);
  }

  /**
   * Get the stream for a given task.
   *
   * @return array(
   *           'task_stream_id' =>,
   *           'timestamp'      =>,
   *           'content'        =>,
   *           'content_type'   =>, [IMAGE, COMMENT]
   *         )
   */
  protected function get_task_stream($task_id) {
    $result = $this->get_db()->query(
      "SELECT task_stream_id, timestamp, content, content_type FROM ".TASK_STREAM_TABLE.
      " WHERE task_id = ".sqlite_escape_string($task_id).
      ";"
    );

    return $this->get_all_results($result);
  }

  /**
   * Create a new task.
   *
   * @return the new task id
   */
  protected function create_task($name, $description) {
    $this->get_db()->exec(
      "INSERT INTO ".TASK_TABLE."(task_title, task_description, is_completed) VALUES(".
      "'".sqlite_escape_string($name)."',".
      "'".sqlite_escape_string($description)."',".
      "0".
      ");"
    );

    return $this->get_db()->lastInsertRowID();
  }


/**
   * Inserts a stream object
   *
   * @param $taskid   The identifier of the task to add this stream object to.
   * @param @type     The type of stream object. ['IMAGE', 'COMMENT']
   * @param @value    A textual value to store in this stream object. For photos, this would
   *                  likely be a short, descriptive note.
   * @return task_stream_id
   */
  protected function insert_stream_object($taskid, $type, $value) {
    $this->get_db()->exec(
      "INSERT INTO task_stream (task_id, timestamp, content, content_type) VALUES(".
      sqlite_escape_string($taskid). ",".
      time().", ".
      "'".sqlite_escape_string($value)."',".
      "'".sqlite_escape_string($type)."'".
      ");"
    );

    return $this->get_db()->lastInsertRowID();
  }
  
/**
   * Gets the meta-stream for a particular stream_id, task_id and key
   *
   * @return value(s) - ['My comment about this design', 'this design is excellent- lets roll with it']
   *      
   */
  protected function get_stream_meta($stream_id, $task_id, $key) {
   $result = $this->get_db()->query(
      "SELECT value FROM ".STREAM_META_TABLE.
      " WHERE task_id = ".sqlite_escape_string($task_id).
      " WHERE stream_id = ".sqlite_escape_string($stream_id).
      " WHERE key = ".sqlite_escape_string($key).
      ";"
    );

    return $this->get_all_results($result);
  }

  /**
   * @param $task_id The task.
   * @param $status  The new status of the task. [true|false]
   */
  protected function set_task_status($task_id, $status) {
    $this->get_db()->exec(
      "UPDATE ".TASK_TABLE." SET is_completed=".$status.
      " WHERE task_id = ".sqlite_escape_string($task_id).";"
    );
  }

  protected function add_js_foot_file($file) {
    $this->template->js_foot_files []= $file;
  }

  protected function add_css_file($file) {
    $this->template->css_files []= $file;
  }

  protected function prepend_title($text) {
    array_unshift($this->template->title, $text);
  }

  protected function render_markdown_template($content) {
    require Kohana::find_file('vendor', 'Markdown');
    $this->template->content = $content->render(FALSE, 'Markdown');

    $this->template->title = implode(' | ', $this->template->title);
    $this->template->render(TRUE);
  }

}
