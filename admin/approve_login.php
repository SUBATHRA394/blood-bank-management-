<?php
// Strict error reporting to see what is failing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connection path check
include('../includes/db.php'); 

if(isset($_GET['id'])) {
    $req_id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. First, find the donor from interests table
    $find_donor = mysqli_query($conn, "SELECT donor_id FROM donation_interests WHERE request_id = '$req_id' LIMIT 1");
    $donor_data = mysqli_fetch_assoc($find_donor);

    if($donor_data) {
        $donor_id = $donor_data['donor_id'];

        // 2. Update the Donor's Count directly (Targeting ID like 7 or 6)
        $update_user = "UPDATE users SET donation_count = donation_count + 1 WHERE id = '$donor_id'";
        
        // 3. Update Request Status to Approved
        $update_req = "UPDATE blood_requests SET status = 'Approved' WHERE id = '$req_id'";
        
        // Execute both
        if(mysqli_query($conn, $update_user) && mysqli_query($conn, $update_req)) {
            // Update Interest status to Completed
            mysqli_query($conn, "UPDATE donation_interests SET status = 'Completed' WHERE request_id = '$req_id'");
            
            // Redirect back to management page
            header("Location: manage_requests.php?success=1");
            exit();
        } else {
            die("Database Error: " . mysqli_error($conn));
        }
    } else {
        // Ithu vanthaal, donation_interests-la antha ID illa nu artham
        die("Error: No volunteer found for Request ID " . $req_id . ". Check your database: image_23c884.jpg");
    }
}
?>
