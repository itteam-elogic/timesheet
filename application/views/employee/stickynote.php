<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Inlude Header here END-->
<link rel="stylesheet" href="<?php echo HTTP_CSS_PATH; ?>stickynotes.css" />
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Courgette" />
<!--[if lt IE 9]>
          <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
<?php

$note_name = 'note.txt';
$uniqueNotePerIP = true;

if($uniqueNotePerIP){
	
	// Use the user's IP as the name of the note.
	// This is useful when you have many people
	// using the app simultaneously.
	
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$note_name = 'uploads/notes/'.$this->session->userdata['logged_in_timesheet']['username'].'.txt';
	}
	else{
		$note_name = 'uploads/notes/'.$this->session->userdata['logged_in_timesheet']['username'].'.txt';
	}
}


if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
	// This is an AJAX request
	
	if(isset($_POST['note'])){
		// Write the file to disk
		file_put_contents($note_name, $_POST['note']);
		echo '{"saved":1}';
	}
	
	exit;
}

$note_content = '';

if( file_exists($note_name) ){
	$note_content = htmlspecialchars( file_get_contents($note_name) );
}

?>
<div class="content-wrapper ">
  <div class="page-title">
    <div>
      <h1>Sticky Note</h1>
    </div>
    
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card notebg">
        <div class="card-body">
          <div class="table-responsive">
            <div id="pad">
			<h2>Note</h2>
			<textarea id="note"><?php echo $note_content ?></textarea>
		    </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Inlude Footer here -->

<script type="text/javascript">
jQuery(function(){
	
	var note = jQuery('#note');
	
	var saveTimer,
		lineHeight = parseInt(note.css('line-height')),
		minHeight = parseInt(note.css('min-height')),
		lastHeight = minHeight,
		newHeight = 0,
		newLines = 0;
		
	var countLinesRegex = new RegExp('\n','g');
	
	// The input event is triggered on key press-es,
	// cut/paste and even on undo/redo.
	
	note.on('input',function(e){
		
		// Clearing the timeout prevents
		// saving on every key press
		clearTimeout(saveTimer);
		saveTimer = setTimeout(ajaxSaveNote, 1000);
		
		// Count the number of new lines
		newLines = note.val().match(countLinesRegex);
		
		if(!newLines){
			newLines = [];
		}
		
		// Increase the height of the note (if needed)
		newHeight = Math.max((newLines.length + 1)*lineHeight, minHeight);
		
		// This will increase/decrease the height only once per change
		if(newHeight != lastHeight){
			note.height(newHeight);
			lastHeight = newHeight;
		}
	}).trigger('input');	// This line will resize the note on page load
	
	function ajaxSaveNote(){
		
		// Trigger an AJAX POST request to save the note
		$.post('<?php echo site_url('stickynote/index');?>', { 'note' : note.val() });
	}
	
});


</script>

<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->

