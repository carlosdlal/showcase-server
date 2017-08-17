<?php
    include('context.php');
    $context = new Context();

    $model = $_POST['model'];
    $method = $_POST['method'];
    $token = $_POST['token'];

    $context->params = isset($_POST['params']) ? $_POST['params']:array();
    //error_log("Params: ".json_encode($_POST['params']));
    include ('models/'.$model.'.php');

    $modelObject = new $model();

    $modelObject->$method($context);

    echo json_encode(array(
        "success"=>1,
        "data"=>$context->response
    ));

    $context->closeDB();
?>
