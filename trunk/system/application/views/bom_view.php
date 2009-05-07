<html>
<head>
<title><?php echo $title; ?></title>
 <link rel="stylesheet" type="text/css" href="http://bom.port-0.com/system/application/views/default.css">
</head>
<body>
<?php 
	//Top Navigation
	echo ('You are now viewing: ' . anchor('bomba_bill/', 'Home Page') . "-->" .anchor('bomba_bill/bom_view/'.$bom_id, $project_codename)); 
	echo br(1); 

	//Create Project Edit Form
	echo form_open('bomba_bill/edit_project');
	echo form_hidden('bom_head',  $bom_id);
	echo heading('Edit Project Details', 1);
	echo $project_form_table;
	echo form_close();
	echo br(1); 
	
	//Create Project Table
	echo heading('Project BOM', 1);
	echo $bom_table;
	echo br(1); 

	/*//Add Existing Part to Project
	echo form_open('bomba_bill/add_part');
	echo heading('Add Existing Part', 1);
	echo $view_table;
	echo br(1);
	echo form_close(); */
	
	//Create New Part Form
	echo form_open('bomba_bill/add_top_part');
	echo form_hidden('bom_head',  $bom_id);
	echo form_hidden('item_id',  $item_id);
	echo heading('Add New Part', 1);
	echo $part_form_table;
	echo br(1);
	echo form_close();
?>
<p><br />Page rendered in {elapsed_time} seconds</p>

</body>
</html>
