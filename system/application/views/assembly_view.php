<html>
<head>
<title><?php echo $title; ?></title>
 <link rel="stylesheet" type="text/css" href="http://bom.port-0.com/system/application/views/default.css">
</head>
<body>
<?php
	$crumbs = "";
	//Top Navigation
	echo ('You are now viewing: '. anchor('bomba_bill/', 'Home Page') . "-->" .anchor('bomba_bill/bom_view/'.$project_id, $project_codename) . "-->"); 
	echo $this->table_utility->build_crumbs($assembly_id, $project_id, $crumbs);
	echo br(1); 
	
	//Create Assembly Table
	echo heading('Assembly BOM', 1);
	echo $assembly_table;
	echo br(1); 
	
	//Create Part Form
	echo form_open('bomba_bill/add_assembly_part');
	echo form_hidden('parent_id',  $assembly_id);
	echo form_hidden('item_id',  $item_id);
	echo heading('Add New Part to Assembly', 1);
	echo $part_table;
	echo br(1);
	echo form_close();
?>
<p><br />Page rendered in {elapsed_time} seconds</p>

</body>
</html>
