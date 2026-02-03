<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? 0;

    if ($action === 'approve_topup') {
        // Get topup info
        $stmt = $pdo->prepare("SELECT * FROM topups WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);
        $topup = $stmt->fetch();

        if ($topup) {
            $pdo->beginTransaction();
            try {
                // Update topup status
                $stmt = $pdo->prepare("UPDATE topups SET status = 'approved' WHERE id = ?");
                $stmt->execute([$id]);

                // Add tokens to user
                $stmt = $pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?");
                $stmt->execute([$topup['amount'], $topup['user_id']]);

                $pdo->commit();
                $_SESSION['flash_message'] = "Top Up disetujui. Token ditambahkan ke user.";
                $_SESSION['flash_type'] = "success";
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['flash_message'] = "Gagal memproses topup.";
                $_SESSION['flash_type'] = "error";
            }
        }
    } elseif ($action === 'reject_topup') {
        $stmt = $pdo->prepare("UPDATE topups SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_message'] = "Top Up ditolak.";
        $_SESSION['flash_type'] = "success";
    }
}

redirect('index.php?page=admin');
?>