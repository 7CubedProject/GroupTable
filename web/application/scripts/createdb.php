<?php 

function createDB($location){
  unlink($location);
  $db = new SQLite3($location);
  chmod($location, 0666);

// create table foo and insert sample data 
  $db->exec("BEGIN; 
          CREATE TABLE task (
            task_id INTEGER PRIMARY KEY,
            task_title VARCHAR(100),
            task_description TEXT,
            is_completed BOOLEAN
          );
          INSERT INTO task (task_title, task_description, is_completed)
            VALUES('logo', 'the task for the logo', 0); 
          INSERT INTO task (task_title, task_description, is_completed)
            VALUES('layout', 'the task for the layout', 0); 
          INSERT INTO task (task_title, task_description, is_completed)
            VALUES('brand', 'the task for the brand', 0); 
          COMMIT;"); 

  $db->exec("BEGIN; 
          CREATE TABLE task_stream (
            task_stream_id INTEGER PRIMARY KEY,
            task_id INTEGER,
            timestamp DATETIME,
            content TEXT,
            content_type VARCHAR(100),
            FOREIGN KEY(task_id) REFERENCES task_stream(task_id)
          );

          INSERT INTO task_stream (task_id, timestamp, content, content_type)
            VALUES(1, ".time().", 'my comment', 'COMMENT'); 
          INSERT INTO task_stream (task_id, timestamp, content, content_type)
            VALUES(1, ".time().", '1.png', 'IMAGE'); 
          INSERT INTO task_stream (task_id, timestamp, content, content_type)
            VALUES(2, ".time().", '2.png', 'IMAGE'); 
          COMMIT;"); 
  
  $db->exec("BEGIN; 
          CREATE TABLE stream_meta (
            stream_meta_id INTEGER PRIMARY KEY,
            task_stream_id INTEGER,
            task_id INTEGER,
            key INTEGER,
            value VARCHAR(100),
            FOREIGN KEY(task_stream_id) REFERENCES task_stream(task_stream_id),
            FOREIGN KEY(task_id) REFERENCES task(task_id)
          );

          INSERT INTO stream_meta (task_stream_id, task_id, key, value)
            VALUES(1, 1, 'key1', 'value1'); 
          INSERT INTO stream_meta (task_stream_id, task_id, key, value)
            VALUES(1, 1,  'key2', 'value2'); 
          INSERT INTO stream_meta (task_stream_id, task_id, key, value)
            VALUES(2, 2, 'key3', 'value3'); 
          COMMIT;"); 

  // execute a query     
  /*
  
  $result = $db->query("SELECT * FROM task"); 
  // iterate through the retrieved rows 
  while ($result->valid()) { 
      // fetch current row 
      $row = $result->current();      
      print_r($row); 
  // proceed to next row 
      $result->next(); 
  } 
  
  
  $result = $db->query("SELECT * FROM task_stream"); 
  // iterate through the retrieved rows 
  while ($result->valid()) { 
      // fetch current row 
      $row = $result->current();      
      print_r($row); 
  // proceed to next row 
      $result->next(); 
  } 
  // not generally needed as PHP will destroy the connection 
  unset($db); 

  
  }
  
  $result = $db->query("SELECT * FROM stream_meta"); 
  // iterate through the retrieved rows 
  while ($result->valid()) { 
      // fetch current row 
      $row = $result->current();      
      print_r($row); 
  // proceed to next row 
      $result->next(); 
  } 
  // not generally needed as PHP will destroy the connection 
  unset($db); 

  
  }*/
}

createDB('../cache/db.sqlite');
