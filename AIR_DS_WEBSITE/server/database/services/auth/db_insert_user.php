<?php
function db_insert_user($conn, $user_data) {
    // the query that inserts the new user
    $query = "  INSERT INTO users(fname, lname, username, password, email) 
                VALUES (:name, :surname, :username, :password, :email)
            ";    
    // prepare the statement
    $stmt = $conn->prepare($query);

    $params = ['name', 'surname', 'username', 'password', 'email'];    
    
    // loop through the parameters and bind them
    foreach($params as $param) {
        $stmt->bindParam(":{$param}", $user_data[$param], PDO::PARAM_STR);
    }    

    // execute the statement
    $stmt->execute();
}
?>