<?php
session_start();

include 'db_connect.php'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];

$appointments = []; 

$sql = "SELECT appointment_id, appointment_date, appointment_time, doctor, status 
        FROM customer_appointments 
        WHERE user_id = ? 
        ORDER BY appointment_date DESC";

if ($stmt = mysqli_prepare($conn, $sql)) {
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
    
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Impian Optometrist</title>
    
    <link rel="stylesheet" href="style.css">     <link rel="stylesheet" href="appointment-style.css"> 
</head>
<body class="appointment-page-body">

    <a href="index.php" class="back-arrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>

    <div class="appointments-container">
        
        <h1>APPOINTMENTS</h1>

        <table>
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>TIME</th>
                    <th>DOCTOR</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (empty($appointments)): 
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #777;">You have no appointments.</td>
                    </tr>
                <?php 
                else: 
                ?>
                    <?php 
                    foreach ($appointments as $appt): 
                    ?>
                    <tr>
                        <td><?php 
                            echo htmlspecialchars(date('jS F Y', strtotime($appt['appointment_date']))); 
                        ?></td>
                        
                        <td><?php 
                            echo htmlspecialchars(date('g A.M.', strtotime($appt['appointment_time']))); 
                        ?></td>
                        
                        <td><?php 
                            echo htmlspecialchars($appt['doctor']); 
                        ?></td>
                        
                        <td>
                            <span class="status-badge status-<?php echo strtolower(htmlspecialchars($appt['status'])); ?>">
                                <?php 
                                echo htmlspecialchars($appt['status']); 
                                ?>
                            </span>
                        </td>
                        
                        <td class="action-cell">
                            <?php if (strtolower($appt['status']) == 'upcoming'): ?>
                                <a href="cancel-appointment.php?id=<?php echo $appt['appointment_id']; ?>" 
                                   class="btn-cancel"
                                   onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                   Cancel
                                </a>
                            <?php endif; ?>
                        </td>
                        </tr>
                    <?php 
                    endforeach; 
                    ?>
                <?php 
                endif; 
                ?>
                 
            </tbody>
        </table>
        
        <div class="book-new-wrapper">
             <a href="book-appointment.php" class="btn-book-new">Book New Appointment</a>
        </div>
    </div> </body>
</html>