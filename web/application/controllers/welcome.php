<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    uwdata
 * @author     Jeff Verkoeyen
 * @copyright  (c) 2010 Jeff Verkoeyen
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class Welcome_Controller extends Standard_Controller {

  const ALLOW_PRODUCTION = TRUE;

  public function index() {
    if (!IN_PRODUCTION) {
      $profiler = new Profiler;
    }

    $this->add_js_foot_file('js/jquery-1.4.min.js');

    $content = new View('home_content');

    $this->render_markdown_template($content);
  }

  public function test() {
    echo json_encode($this->get_task_list());
    //$this->set_task_status(15, 1);
  }

} // End Welcome Controller
