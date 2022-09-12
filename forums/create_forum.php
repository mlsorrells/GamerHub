<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
if($user['level'] != "3"){
header('location:index.php');
}
if(isset($_POST['create'])){
$name = $_POST['name'];
$name = strip_tags($name);
$name = htmlentities($name);
$name = $conn->real_escape_string($name);
$description = $_POST['description'];
$description = strip_tags($description);
$description = htmlentities($description);
$description = $conn->real_escape_string($description);
$check_query = $conn->query("SELECT * FROM categories WHERE name='$name'");
$check = $check_query->num_rows;
if($name == "" || $description == ""){
$fields = "All fields are required.";
}elseif($check == "1"){
$checked = "This forum already exists.";
}else{
$insert = $conn->query("INSERT INTO categories(name, description) VALUES('$name','$description')");
$success = "Forum created.";
}
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - Create Forum</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Create a New Forum</h3>
<a href="index.php">Back to Forums</a>
<div class="inner">
<?php
echo $fields;
echo $checked;
echo $success;
?>
<form action="" method="post">
<label for="name">Name</label><br/>
<input type="text" name="name" id="name"/><br/>
<label for="description">Description</label></br/>
<textarea name="description" id="description" rows="5" cols="30"></textarea><br/>
<input type="submit" name="create" value="Create Forum"/>
</form>
</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>