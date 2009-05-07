<html>
<head>
<title><?php echo $title; ?></title>
 <link rel="stylesheet" type="text/css" href="http://bom.port-0.com/system/application/views/default.css">
</head>
<body>
<?php 
	//Top Navigation
	echo ('You are now viewing: ' . anchor('bomba_bill/', 'Home Page')); 
	echo br(1); 
	echo heading('Select Project', 1);
	
	//Create Project Table
	echo $project_table;
	echo br(1); 
	echo heading('Create Project', 1); 
	
	//Create Form
	echo form_open('bomba_bill/create_project');
	echo form_hidden('bom_head',  $bom_id);
	echo form_hidden('entry_date', date("Y-m-d"));
	echo "Next ID: " . $bom_id;
	echo $form_table;
	echo form_close()

?>

<p> Todo </p>
<ol>
<?php foreach($todo as $item): ?>
<li><?=$item?></li>
<?php endforeach; ?>

<p><br />Page rendered in {elapsed_time} seconds</p>

</body>
</html>
