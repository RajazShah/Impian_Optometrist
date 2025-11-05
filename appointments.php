<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Appointments</title>
    <link rel="stylesheet" href="appointments.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="appointments-container">
        <h2>Staff Appointments</h2>

        <div class="table-responsive">
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Staff NRIC</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'db_connect.php';

                    $sql = "SELECT appointment_id, staff_nric, doctor, appointment_date, appointment_time, reason, status
                            FROM staff_appointments
                            ORDER BY appointment_date DESC, appointment_time DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["appointment_id"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["staff_nric"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["doctor"]) . "</td>";
                            echo "<td>" . htmlspecialchars(date('M d, Y', strtotime($row["appointment_date"]))) . "</td>"; // Format date
                            echo "<td>" . htmlspecialchars(date('h:i A', strtotime($row["appointment_time"]))) . "</td>"; // Format time
                            echo "<td>" . htmlspecialchars($row["reason"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align: center;'>No appointments found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div> <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>

    </div> </body>
</html>