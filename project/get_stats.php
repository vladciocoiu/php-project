<?php
    function getTotalHomeViews($conn) {
        $sql = 'SELECT * FROM home_views';

        $result = $conn->query($sql);
        return $result->num_rows;
    }

    function getUniqueVisitors($conn) {
        $sql = 'SELECT * FROM visitors';

        $result = $conn->query($sql);
        return $result->num_rows;
    }

    function incrementHomeViews($conn) {
        $user_ip = $_SERVER['REMOTE_ADDR'];

        $check_sql = "SELECT * FROM visitors WHERE ip_address = '$user_ip'";
        $result = $conn->query($check_sql);

        if($result->num_rows === 0) {
            $sql = "INSERT INTO visitors (ip_address) VALUES ('$user_ip')";
            $conn->query($sql);
        }

        $views_sql = "INSERT INTO home_views VALUES ()";
        $conn->query($views_sql);
    }
?>