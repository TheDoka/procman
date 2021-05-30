<?php
    
    $filename = uniqid();

    if ( 0 < $_FILES['file']['error'] ) {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        move_uploaded_file($_FILES['file']['tmp_name'], '../../uploads/' . $filename);
    }
        
    echo json_encode(array(
        'success' => 1,
        'file'   => array(
            'url' => 'uploads/' . $filename,
            'name' => $_FILES['file']['name'], 
            'size' => $_FILES['file']['size']
        ),
    ));



?>