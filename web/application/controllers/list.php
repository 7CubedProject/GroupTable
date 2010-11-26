<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package    GroupTable
 * @author     
 * @copyright  
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class List_Controller extends Standard_Controller {

  const ALLOW_PRODUCTION = TRUE;

  public function index() {
    if (!IN_PRODUCTION) {
      $profiler = new Profiler;
    }

    $this->add_js_foot_file('js/jquery-1.4.min.js');

    $content = new View('home_content');

    $this->render_markdown_template($content);
  }

  private function genVenueListHtml($groupSize, $venueList) {
    return '<li>HELLO</li>';
  }

  public function showList() {
    $this->add_js_foot_file('js/jquery-1.4.min.js');
    $this->add_js_foot_file('js/list.js');
    $content = new View('list_content');
    $this->render_markdown_template($content);
  }
  
  public function getList() {
    $groupSize = $_REQUEST['size'];

    echo json_encode($this->get_venue_list($groupSize));
    return;

    echo "Welcome to the venuelist!";

    $venueList= $this->get_venue_list($groupSize);
    $listHtml = $this->genVenueListHtml($groupSize, $venueList);

    $content = new View('list_content');
    $content = str_replace("!!!", $listHtml, $content);

    $this->render_markdown_template($content);
    // do some shit to put it to the screen prettily
  }

} // End List Controller
