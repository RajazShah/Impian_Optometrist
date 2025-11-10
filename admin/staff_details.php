<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Details</title>
    <link rel="stylesheet" href="staff_details.css">
</head>
<body>
    <div class="appointments-container">
        <h2>Staff Details</h2>
        <table class="appointments-table">
            <tr>
                <th>NRIC</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Hire Date</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php
            include '../db_connect.php';    
            if (isset($_GET['action']) && isset($_GET['nric'])) {
                $nric = $_GET['nric'];
                $action = $_GET['action'];

                if ($action == "resign") {
                    $update = "UPDATE staff SET STAFF_STATUS='Resigned' WHERE STAFF_NRIC='$nric'";
                } elseif ($action == "rehire") {
                    $update = "UPDATE staff SET STAFF_STATUS='Active' WHERE STAFF_NRIC='$nric'";
                }

                $conn->query($update);
            }

            $sql = "SELECT * FROM staff";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["STAFF_NRIC"] . "</td>";
                    echo "<td>" . $row["STAFF_FNAME"] . "</td>";
                    echo "<td>" . $row["STAFF_LNAME"] . "</td>";
                    echo "<td>" . $row["STAFF_PHONE"] . "</td>";
                    echo "<td>" . $row["STAFF_HIRE_DATE"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    $status = $row["STAFF_STATUS"] ?? 'Active';
                    echo "<td>" . $status . "</td>";

                    if ($status == 'Active') {
                        echo "<td><a href='?action=resign&nric=" . $row["STAFF_NRIC"] . "' class='btn-resign' onclick=\"return confirm('Are you sure you want to resign this staff?');\">Resign</a></td>";
                    } else {
                        echo "<td><a href='?action=rehire&nric=" . $row["STAFF_NRIC"] . "' class='btn-rehire' onclick=\"return confirm('Rehire this staff?');\">Rehire</a></td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No staff found</td></tr>";
            }

            $conn->close();
            ?>
        </table>

        <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>
    </div>

</body>
</html>
