<?php
session_start();
include "db_connect.php";
ini_set("display_errors", 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password_from_form = $_POST["password"];

    if ($email === "admin@impian.com" && $password_from_form === "admin123") {
        $_SESSION["loggedin"] = true;
        $_SESSION["email"] = $email;
        $_SESSION["role"] = "admin";
        header("Location: admin/admin_page.php");
        exit();
    }

    $sql_staff =
        "SELECT STAFF_NRIC, staff_fname, password FROM staff WHERE email = ?";
    if ($stmt_staff = mysqli_prepare($conn, $sql_staff)) {
        mysqli_stmt_bind_param($stmt_staff, "s", $email);
        if (mysqli_stmt_execute($stmt_staff)) {
            mysqli_stmt_store_result($stmt_staff);
            if (mysqli_stmt_num_rows($stmt_staff) == 1) {
                mysqli_stmt_bind_result(
                    $stmt_staff,
                    $staff_nric_from_db,
                    $staff_fname_from_db,
                    $hashed_password_from_db,
                );
                if (mysqli_stmt_fetch($stmt_staff)) {
                    if (
                        password_verify(
                            $password_from_form,
                            $hashed_password_from_db,
                        )
                    ) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["staff_ic"] = $staff_nric_from_db;
                        $_SESSION["email"] = $email;
                        $_SESSION["first_name"] = $staff_fname_from_db;
                        $_SESSION["role"] = "staff";
                        mysqli_stmt_close($stmt_staff);
                        mysqli_close($conn);
                        header("Location: admin_page.php");
                        exit();
                    }
                }
            }
        } else {
            echo "Oops! Something went wrong checking staff. Please try again later.";
        }
        mysqli_stmt_close($stmt_staff);
    } else {
        echo "Oops! Error preparing staff check. Please try again later.";
    }

    $sql_user = "SELECT id, first_name, password FROM users WHERE email = ?";
    if ($stmt_user = mysqli_prepare($conn, $sql_user)) {
        mysqli_stmt_bind_param($stmt_user, "s", $email);
        if (mysqli_stmt_execute($stmt_user)) {
            mysqli_stmt_store_result($stmt_user);
            if (mysqli_stmt_num_rows($stmt_user) == 1) {
                mysqli_stmt_bind_result(
                    $stmt_user,
                    $id,
                    $first_name,
                    $hashed_password_from_db,
                );
                if (mysqli_stmt_fetch($stmt_user)) {
                    if (
                        password_verify(
                            $password_from_form,
                            $hashed_password_from_db,
                        )
                    ) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["email"] = $email;
                        $_SESSION["first_name"] = $first_name;
                        $_SESSION["role"] = "user";
                        mysqli_stmt_close($stmt_user);
                        mysqli_close($conn);
                        header("Location: index.php");
                        exit();
                    }
                }
            }
        } else {
            echo "Oops! Something went wrong checking users. Please try again later.";
        }
        mysqli_stmt_close($stmt_user);
    } else {
        echo "Oops! Error preparing user check. Please try again later.";
    }

    mysqli_close($conn);
    echo "<script>alert('Invalid email or password.'); window.history.back();</script>";
    exit();
}
?>
