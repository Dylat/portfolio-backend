<?php

require "vendor/autoload.php";
require 'database.php';
require 'blog.php';

header('content-type: application/json');

$response = array(
	'success' => false
);

$phpInput = array();
$method = strtoupper($_SERVER['REQUEST_METHOD']);


if ('PUT' === $method || 'DELETE' === $method || 'POST' === $method) {
    $phpInput = json_decode(file_get_contents('php://input'), true);
} 
 
$httpRequest = array_merge(
	$_GET,
	$_POST,
	$phpInput
);


$Blog = new Blog($DB);







if ($method == "GET" || ($method == "POST" && isset($httpRequest['options']))) {
	$id = isset($httpRequest['id']) ? intval($httpRequest['id']) : 0;
	if (!empty($id)) {
		$post = $Blog->read($id);
		$response['success'] = true;
		$response['post'] = $post;
	} else {
		$posts  = $Blog->list(isset($httpRequest['options']) ? $httpRequest['options'] : array());
		$response['posts'] = $posts; 
		$response['success'] = true;
	}

}





if ($method == "POST" && !isset($_POST['options']))
{

	$data = isset($httpRequest['data']) ? $httpRequest['data'] : array();


	if (is_array($data) && count($data) > 0) {
		$response['success'] = !empty($Blog->create($data));
		$id = $Blog->create($data);


		$response['success'] = !empty($id);

		if ($response['success']) {
			$response['id'] = $id;
		}
	}
}





if ($method  === "PUT")
{
	$id = isset($httpRequest['id']) ? intval($httpRequest['id']) : 0;
	$data = isset($httpRequest['data']) ? $httpRequest['data'] : array();

	if (!empty($id) && is_array($data) && count($data) > 0) {
		$response['success'] = $Blog->update($id, $data);
	}
}






if ($method === "DELETE") {
	$id = isset($httpRequest['id']) ? intval($httpRequest['id']) : 0;

	if (!empty($id)) {
		$response['success'] = $Blog->delete($id);
	}
}


$requireesponse['httpRequest'] = $httpRequest;

echo json_encode($response);
exit;