<?php
// log_activity.php
// Utility functions for logging user actions (activity_log) & updating favorite_drugs.

function logActivity($db, $user_id, $event_type, $details = null) {
    $stmt = $db->prepare("
        INSERT INTO activity_log (user_id, event_type, event_details, created_at)
        VALUES (:uid, :etype, :edetails, NOW())
    ");
    $stmt->execute([
        ':uid' => $user_id,
        ':etype' => $event_type,
        ':edetails' => $details
    ]);
}

function updateFavoriteDrug($db, $user_id, $drugName) {
    // Check if this user already has a row for this drug
    $stmt = $db->prepare("
        SELECT id, search_count 
        FROM favorite_drugs
        WHERE user_id = :uid AND drug_name = :drug
    ");
    $stmt->execute([
        ':uid' => $user_id,
        ':drug' => $drugName
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // update
        $stmt2 = $db->prepare("
            UPDATE favorite_drugs
            SET search_count = search_count + 1,
                last_searched = NOW()
            WHERE id = :id
        ");
        $stmt2->execute([':id' => $row['id']]);
    } else {
        // insert
        $stmt3 = $db->prepare("
            INSERT INTO favorite_drugs (user_id, drug_name, search_count, last_searched)
            VALUES (:uid, :drug, 1, NOW())
        ");
        $stmt3->execute([
            ':uid' => $user_id,
            ':drug' => $drugName
        ]);
    }
}
