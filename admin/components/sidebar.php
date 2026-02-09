<?php
// Sidebar component réutilisable pour l'admin
?>
<nav class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-brand">
        <img src="../ressources/logo.png" alt="Logo" class="brand-logo" />
        <div class="brand-text">
            <span class="brand-name">Metrix</span>
            <span class="brand-sub">ADMIN</span>
        </div>
    </div>

    <div class="admin-sidebar-header">
        <h2>Administration</h2>
    </div>

    <div class="admin-sidebar-content">
        <div class="admin-nav-section">
            <h3 class="admin-nav-title">Gestion des clauses</h3>
            <a href="index.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Ajouter une clause</span>
            </a>
        </div>

        <div class="admin-nav-section">
            <a href="ajouter-domaine.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) === 'ajouter-domaine.php' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span>Ajouter un domaine</span>
            </a>
        </div>

        <div class="admin-nav-section">
            <h3 class="admin-nav-title">Gestion des utilisateurs</h3>
            <a href="ajouter-user.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) === 'ajouter-user.php' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" y1="8" x2="19" y2="14"></line>
                    <line x1="22" y1="11" x2="16" y2="11"></line>
                </svg>
                <span>Ajouter un utilisateur</span>
            </a>
            <a href="liste-users.php" class="admin-nav-link <?= basename($_SERVER['PHP_SELF']) === 'liste-users.php' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"></path>
                    <path d="M3 12h18"></path>
                    <path d="M3 18h18"></path>
                </svg>
                <span>Liste des utilisateurs</span>
            </a>
        </div>
    </div>

    <div class="admin-sidebar-footer">
        <div class="admin-user-info">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Administrateur</span>
        </div>
    </div>
</nav>
