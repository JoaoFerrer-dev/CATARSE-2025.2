<?php
// admin_auth.php - Verificar se usuário é administrador
session_start();

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true && isset($_SESSION['admin_id']);
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../admin/login.php');
        exit();
    }
}

function isSuperAdmin() {
    return isAdmin() && isset($_SESSION['admin_nivel']) && $_SESSION['admin_nivel'] === 'super_admin';
}

function requireSuperAdmin() {
    if (!isSuperAdmin()) {
        header('Location: ../admin/dashboard.php?error=acesso_negado');
        exit();
    }
}
?>

