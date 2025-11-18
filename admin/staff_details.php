<?php
include "../db_connect.php";

$showAlert = false;
$alertMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST["action_type"]) &&
        $_POST["action_type"] == "create_staff"
    ) {
        $nric = $_POST["staff_nric"];
        $fname = $_POST["staff_fname"];
        $lname = $_POST["staff_lname"];
        $phone = $_POST["staff_phone"];
        $email = $_POST["staff_email"];
        $raw_password = $_POST["staff_password"];
        $status = "Active";

        $hire_date = date("Y-m-d");

        $yy = substr($nric, 0, 2);
        $mm = substr($nric, 2, 2);
        $dd = substr($nric, 4, 2);

        $current_year_short = date("y");
        $century = $yy > $current_year_short ? "19" : "20";
        $dob = $century . $yy . "-" . $mm . "-" . $dd;

        $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

        $stmt_check = $conn->prepare(
            "SELECT STAFF_NRIC FROM staff WHERE STAFF_NRIC = ?",
        );
        $stmt_check->bind_param("s", $nric);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $alertMessage = "Error: Staff with NRIC $nric already exists.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO staff (STAFF_NRIC, STAFF_FNAME, STAFF_LNAME, STAFF_PHONE, email, STAFF_HIRE_DATE, STAFF_DATE_OF_BIRTH, STAFF_STATUS, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            );
            $stmt->bind_param(
                "sssssssss",
                $nric,
                $fname,
                $lname,
                $phone,
                $email,
                $hire_date,
                $dob,
                $status,
                $hashed_password,
            );

            if ($stmt->execute()) {
                $alertMessage = "Staff member created successfully!";
                $stmt->close();
            } else {
                $alertMessage = "Error creating staff: " . $conn->error;
            }
        }
        $stmt_check->close();
        $showAlert = true;
    }
}

if (isset($_GET["action"]) && isset($_GET["nric"])) {
    $nric = $_GET["nric"];
    $action = $_GET["action"];
    $new_status = "";

    if ($action == "resign") {
        $new_status = "Resigned";
    } elseif ($action == "rehire") {
        $new_status = "Active";
    }

    if (!empty($new_status)) {
        $stmt_update = $conn->prepare(
            "UPDATE staff SET STAFF_STATUS = ? WHERE STAFF_NRIC = ?",
        );
        $stmt_update->bind_param("ss", $new_status, $nric);
        $stmt_update->execute();
        $stmt_update->close();
    }
    header("Location: " . strtok($_SERVER["REQUEST_URI"], "?"));
    exit();
}

$search_query = isset($_GET["search_query"]) ? $_GET["search_query"] : "";
$current_page_url = strtok($_SERVER["REQUEST_URI"], "?");
?>

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
        <div class="container-header" style="display: flex; justify-content: space-between; align-items: center; padding: 24px 28px; border-bottom: 1px solid #eef2f9;">
            <h2 style="border:none; padding:0; margin:0;">Staff Details</h2>
            <div class="header-buttons">
                <a href="admin_page.php" class="btn-back" style="margin:0; margin-right: 10px;">&larr; Back</a>
                <button id="showCreateFormBtn" class="btn-create" style="background-color: #10b981; color: white; border: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; cursor: pointer;">+ Create New Staff</button>
            </div>
        </div>

        <div class="search-container">
            <form method="GET" action="<?php echo $current_page_url; ?>" style="display:contents;">
                <label for="search_query">Search Staff:</label>
                <input type="text" id="search_query" name="search_query" value="<?php echo htmlspecialchars(
                    $search_query,
                ); ?>" placeholder="Enter NRIC or Name...">
                <button type="submit">Search</button>
                <a href="<?php echo $current_page_url; ?>">Clear</a>
            </form>
        </div>

        <div id="createFormContainer" class="create-form-container" style="display:none; padding: 20px 28px; background-color: #fdfdff; border-bottom: 1px solid #eef2f9;">
            <h3 style="margin-top: 0;">Register New Staff</h3>
            <form method="POST" action="<?php echo $current_page_url; ?>">
                <input type="hidden" name="action_type" value="create_staff">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="display:block; margin-bottom: 5px; font-weight:600; color:#4a5568;">NRIC (ID)</label>
                        <input type="text" name="staff_nric" required style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;" placeholder="e.g. 98010110...">
                    </div>
                    <div class="form-group">
                        <label style="display:block; margin-bottom: 5px; font-weight:600; color:#4a5568;">Password</label>
                        <input type="password" name="staff_password" required style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
                    </div>
                    <div class="form-group">
                        <label style="display:block; margin-bottom: 5px; font-weight:600; color:#4a5568;">First Name</label>
                        <input type="text" name="staff_fname" required style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
                    </div>
                    <div class="form-group">
                        <label style="display:block; margin-bottom: 5px; font-weight:600; color:#4a5568;">Last Name</label>
                        <input type="text" name="staff_lname" required style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
                    </div>
                    <div class="form-group">
                        <label style="display:block; margin-bottom: 5px; font-weight:600; color:#4a5568;">Email</label>
                        <input type="email" name="staff_email" required style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
                    </div>
                    <div class="form-group">
                        <label style="display:block; margin-bottom: 5px; font-weight:600; color:#4a5568;">Phone Number</label>
                        <input type="text" name="staff_phone" required style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
                    </div>
                </div>
                <p style="font-size: 0.85rem; color: #666; margin-top: -10px; margin-bottom: 15px;">* Date of Birth and Hire Date will be generated automatically.</p>
                <button type="submit" style="background-color: #4299e1; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">Save New Staff</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="appointments-table">
                <tr>
                    <th>NRIC</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Hire Date</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php
                $sql = "SELECT * FROM staff";
                $params = [];
                $types = "";

                if (!empty($search_query)) {
                    $sql .=
                        " WHERE (STAFF_NRIC LIKE ? OR STAFF_FNAME LIKE ? OR STAFF_LNAME LIKE ?)";
                    $search_term = "%" . $search_query . "%";
                    $params = [$search_term, $search_term, $search_term];
                    $types = "sss";
                }

                $stmt_select = $conn->prepare($sql);

                if (!empty($params)) {
                    $stmt_select->bind_param($types, ...$params);
                }

                $stmt_select->execute();
                $result = $stmt_select->get_result();

                $search_param_url = !empty($search_query)
                    ? "&search_query=" . urlencode($search_query)
                    : "";

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" .
                            htmlspecialchars($row["STAFF_NRIC"]) .
                            "</td>";
                        echo "<td>" .
                            htmlspecialchars(
                                $row["STAFF_FNAME"] . " " . $row["STAFF_LNAME"],
                            ) .
                            "</td>";
                        echo "<td>" .
                            htmlspecialchars($row["STAFF_PHONE"]) .
                            "</td>";
                        echo "<td>" .
                            htmlspecialchars($row["STAFF_HIRE_DATE"]) .
                            "</td>";
                        echo "<td>" .
                            htmlspecialchars($row["STAFF_DATE_OF_BIRTH"]) .
                            "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";

                        $status = $row["STAFF_STATUS"] ?? "Active";
                        echo "<td>" . htmlspecialchars($status) . "</td>";

                        if ($status == "Active") {
                            echo "<td><a href='?action=resign&nric=" .
                                urlencode($row["STAFF_NRIC"]) .
                                $search_param_url .
                                "' class='btn-resign' onclick=\"return confirm('Resign this staff member?');\">Resign</a></td>";
                        } else {
                            echo "<td><a href='?action=rehire&nric=" .
                                urlencode($row["STAFF_NRIC"]) .
                                $search_param_url .
                                "' class='btn-rehire' onclick=\"return confirm('Rehire this staff member?');\">Rehire</a></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No staff found.</td></tr>";
                }

                $stmt_select->close();
                $conn->close();

                if ($showAlert) {
                    $jsAlertMessage = addslashes($alertMessage);
                    echo "<script>alert('$jsAlertMessage'); window.location.href='$current_page_url';</script>";
                }
                ?>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('showCreateFormBtn').addEventListener('click', function() {
            var form = document.getElementById('createFormContainer');
            if (form.style.display === 'none') {
                form.style.display = 'block';
                this.textContent = 'Cancel';
                this.style.backgroundColor = '#ef4444';
            } else {
                form.style.display = 'none';
                this.textContent = '+ Create New Staff';
                this.style.backgroundColor = '#10b981';
            }
        });
    </script>
</body>
</html>
