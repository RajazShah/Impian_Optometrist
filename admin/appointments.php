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
                <th>User ID</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Reason</th>
                <th>Status</th>
            </tr>

            <?php
            include "../db_connect.php";

            $sql = "SELECT 
                        appointment_id, 
                        user_id, 
                        doctor, 
                        appointment_date AS date, 
                        appointment_time AS time, 
                        reason, 
                        status 
                    FROM customer_appointments";
            
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["appointment_id"] . "</td>";
                    echo "<td>" . $row["user_id"] . "</td>"; 
                    echo "<td>" . $row["doctor"] . "</td>";
                    echo "<td>" . $row["appointment_date"] . "</td>";
                    echo "<td>" . $row["appointment_time"] . "</td>";
                    echo "<td>" . $row["reason"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "</tr>";
                }
            } else {
                // Fixed colspan to 7
                echo "<tr><td colspan='7'>No appointments found</td></tr>";
            }
            ?>

        </table>
        <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>
    </div>

</body>
</html>