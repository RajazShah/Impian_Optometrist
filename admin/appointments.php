<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="appointments.css">
</head>
<body>
    <div class="appointments-container">
        <h2>Customer Appointments</h2>
        <table class="appointments-table">
            <tr>
                <th>Appointment ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Date</th>
                <th>Time</th>
            </tr>

            <?php
            include '../db_connect.php';

            $sql = "SELECT * FROM appointments";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["appointment_id"] . "</td>";
                    echo "<td>" . $row["user_id"] . "</td>";
                    echo "<td>" . $row["doctor"] . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "<td>" . $row["time"] . "</td>";
                    echo "<td>" . $row["reason"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No appointments found</td></tr>";
            }
            ?>
            
        </table>
        <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>
    </div>

</body>
</html>
