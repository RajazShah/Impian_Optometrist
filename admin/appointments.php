<?php
include "../db_connect.php";

$filter_date = $_GET["filter_date"] ?? "";
$filter_status = $_GET["filter_status"] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["appointment_id"])) {
    $appointment_id = $_POST["appointment_id"];
    $new_status = $_POST["status"];

    $stmt = $conn->prepare(
        "UPDATE customer_appointments SET status = ? WHERE appointment_id = ?",
    );
    $stmt->bind_param("si", $new_status, $appointment_id);

    if ($stmt->execute()) {
        $redirect_url = "appointments.php";
        $query_params = [];

        if (isset($_POST["filter_date"]) && !empty($_POST["filter_date"])) {
            $query_params["filter_date"] = $_POST["filter_date"];
        }
        if (isset($_POST["filter_status"]) && !empty($_POST["filter_status"])) {
            $query_params["filter_status"] = $_POST["filter_status"];
        }

        if (!empty($query_params)) {
            $redirect_url .= "?" . http_build_query($query_params);
        }

        header("Location: " . $redirect_url);
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

        <div class="filter-container">
            <form action="appointments.php" method="GET">
                <div class="filter-group">
                    <label for="filter_date">Filter by Date:</label>
                    <input type="date" id="filter_date" name="filter_date" value="<?php echo htmlspecialchars(
                        $filter_date,
                    ); ?>">
                </div>

                <div class="filter-group">
                    <label for="filter_status">Filter by Status:</label>
                    <select id="filter_status" name="filter_status">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo $filter_status ==
                        "Pending"
                            ? "selected"
                            : ""; ?>>Pending</option>
                        <option value="Completed" <?php echo $filter_status ==
                        "Completed"
                            ? "selected"
                            : ""; ?>>Completed</option>
                        <option value="Cancelled" <?php echo $filter_status ==
                        "Cancelled"
                            ? "selected"
                            : ""; ?>>Cancelled</option>
                    </select>
                </div>

                <div class="filter-group">
                    <button type="submit" class="btn-filter">Filter</button>
                    <a href="appointments.php" class="btn-clear">Clear</a>
                </div>
            </form>
        </div>

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
                    FROM customer_appointments";

            $where_clauses = [];
            $params = [];
            $types = "";

            if (!empty($filter_date)) {
                $where_clauses[] = "appointment_date = ?";
                $params[] = $filter_date;
                $types .= "s";
            }
            if (!empty($filter_status)) {
                $where_clauses[] = "status = ?";
                $params[] = $filter_status;
                $types .= "s";
            }

            if (!empty($where_clauses)) {
                $sql .= " WHERE " . implode(" AND ", $where_clauses);
            }

            $sql .= " ORDER BY appointment_date DESC, appointment_time DESC";

            $stmt_select = $conn->prepare($sql);

            if (!empty($params)) {
                $stmt_select->bind_param($types, ...$params);
            }

            $stmt_select->execute();
            $result = $stmt_select->get_result();

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

                    echo "<td>";
                    echo "<form action='appointments.php' method='POST' class='status-form' style='margin: 0;'>";
                    echo "<input type='hidden' name='appointment_id' value='" .
                        $row["appointment_id"] .
                        "'>";

                    echo "<input type='hidden' name='filter_date' value='" .
                        htmlspecialchars($filter_date) .
                        "'>";
                    echo "<input type='hidden' name='filter_status' value='" .
                        htmlspecialchars($filter_status) .
                        "'>";

                    echo "<select name='status' onchange='this.form.submit()'>";

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
                if (!empty($filter_date) || !empty($filter_status)) {
                    echo "<tr><td colspan='8'>No appointments found matching your filters.</td></tr>";
                } else {
                    echo "<tr><td colspan='8'>No appointments found</td></tr>";
                }
            }
            $stmt_select->close();
            ?>

        </table>
        <a href="admin_page.php" class="btn-back">&larr; Back to Dashboard</a>
    </div>

</body>
</html>
