<?php
include "../db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["appointment_id"])) {
    $appointment_id = $_POST["appointment_id"];
    $new_status = $_POST["status"];

    $stmt = $conn->prepare(
        "UPDATE customer_appointments SET status = ? WHERE appointment_id = ?",
    );
    $stmt->bind_param("si", $new_status, $appointment_id);

    if ($stmt->execute()) {
        header("Location: appointments.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
}

function get_status_span($status)
{
    $status_lower = strtolower($status);
    $class = "status-" . $status_lower;
    $text = ucfirst($status_lower);

    if (!in_array($status_lower, ["completed", "pending", "cancelled"])) {
        $class = "status-other";
    }

    return "<span class=\"status {$class}\">" .
        htmlspecialchars($text) .
        "</span>";
}
?>
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
                <th>Current Status</th>
                <th>Change Status</th>
            </tr>

            <?php
            $sql = "SELECT
                        appointment_id,
                        user_id,
                        doctor,
                        appointment_date AS date,
                        appointment_time AS time,
                        reason,
                        status
                    FROM customer_appointments
                    ORDER BY appointment_date DESC, appointment_time DESC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["appointment_id"] . "</td>";
                    echo "<td>" . $row["user_id"] . "</td>";
                    echo "<td>" . htmlspecialchars($row["doctor"]) . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "<td>" . $row["time"] . "</td>";
                    echo "<td>" . htmlspecialchars($row["reason"]) . "</td>";

                    echo "<td>" . get_status_span($row["status"]) . "</td>";

                    // This form submits automatically when the dropdown is changed
                    echo "<td>";
                    echo "<form action='appointments.php' method='POST' class='status-form' style='margin: 0;'>";
                    echo "<input type='hidden' name='appointment_id' value='" .
                        $row["appointment_id"] .
                        "'>";

                    // The onchange='this.form.submit()' is the key
                    echo "<select name='status' onchange='this.form.submit()'>";

                    // These lines make the dropdown default to the current status
                    $current_status_lower = strtolower($row["status"]);
                    $isPending =
                        $current_status_lower == "pending" ? "selected" : "";
                    $isCompleted =
                        $current_status_lower == "completed" ? "selected" : "";
                    $isCancelled =
                        $current_status_lower == "cancelled" ? "selected" : "";

                    echo "<option value='Pending' $isPending>Pending</option>";
                    echo "<option value='Completed' $isCompleted>Completed</option>";
                    echo "<option value='Cancelled' $isCancelled>Cancelled</option>";

                    echo "</select>";
                    echo "</form>";
                    echo "</td>";

                    echo "</tr>";
                }
            } else {
                // Colspan is now 8
                echo "<tr><td colspan='8'>No appointments found</td></tr>";
            }
            ?>

        </table>
        <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>
    </div>

</body>
</html>
