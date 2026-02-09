<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metrix - DISI</title>
    <link rel="stylesheet" href="styles/main.css" />
    <!-- Chart.js chargé en defer pour ne pas bloquer le rendu -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
</head>

<body>
    <div style="position: fixed; top: 10px; right: 10px; z-index: 1000; background: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 10px; font-size: 12px;">
        <span><?= htmlspecialchars($user['email']) ?></span>
        <a href="logout.php" style="margin-left: 8px;">Deconnexion</a>
    </div>
    <!-- Sommaire de navigation -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <div class="container" id="main-content">
        <div class="stats-overview">
            <div class="stats-header">
                <span class="stats-title">Vue globale</span>
                <span class="stats-progress"><?= $clausesWithNotes ?>/<?= $totalClauses ?> clauses évaluées</span>
            </div>
            <div class="stats-bars">
                <div class="stats-bar-item">
                    <span class="stats-bar-label">Non conforme</span>
                    <div class="stats-bar-track">
                        <div class="stats-bar-fill stats-low" style="width: <?= $clausesWithNotes > 0 ? round(($notesLow / $clausesWithNotes) * 100) : 0 ?>%"></div>
                    </div>
                    <span class="stats-bar-count"><?= $notesLow ?></span>
                </div>
                <div class="stats-bar-item">
                    <span class="stats-bar-label">Moyen Conforme</span>
                    <div class="stats-bar-track">
                        <div class="stats-bar-fill stats-mid" style="width: <?= $clausesWithNotes > 0 ? round(($notesMid / $clausesWithNotes) * 100) : 0 ?>%"></div>
                    </div>
                    <span class="stats-bar-count"><?= $notesMid ?></span>
                </div>
                <div class="stats-bar-item">
                    <span class="stats-bar-label">Conforme</span>
                    <div class="stats-bar-track">
                        <div class="stats-bar-fill stats-high" style="width: <?= $clausesWithNotes > 0 ? round(($notesHigh / $clausesWithNotes) * 100) : 0 ?>%"></div>
                    </div>
                    <span class="stats-bar-count"><?= $notesHigh ?></span>
                </div>
            </div>
            <?php if ($globalAverage !== null): ?>
                <div class="stats-average">
                    Moyenne globale: <strong><?= $globalAverage ?>/2</strong>
                </div>
            <?php endif; ?>
        </div>

        <!-- Section Favoris -->
        <div class="favorites-section" id="favorites-section" style="display: none;">
            <div class="favorites-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="var(--c-gold)" stroke="var(--c-gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                <span class="favorites-title">Mes Favoris</span>
                <span class="favorites-count" id="favorites-count">0</span>
            </div>
            <div class="favorites-list" id="favorites-list">
                <!-- Les favoris seront injectés ici par JS -->
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="clause-search" placeholder="Rechercher une clause" autocomplete="off" />
            <span class="search-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <span id="search-results-count" class="search-results-count"></span>
        </div>
        <!-- Ont fais une boucle sur les domaines -->
        <?php $isFirstDomain = true; ?>
        <?php foreach ($rowsByDomain as $domainId => $domainData): ?>
            <?php $isOpen = $isFirstDomain;
            $isFirstDomain = false; ?>
            <div class="domain-section" data-domain-id="<?= htmlspecialchars($domainId) ?>">
                <div class="domain-header">
                    <h3 class="domain-title"><?= htmlspecialchars($domainData['nom']) ?></h3>
                    <button class="domain-toggle-btn" data-action="domain-toggle">
                        <?= $isOpen ? 'Masquer les clauses' : 'Voir les clauses' ?>
                        <span class="domain-toggle-icon">▾</span>
                    </button>
                </div>
                <!-- dans les domaines ont récupére les clauses -->
                <div class="clauses-list<?= $isOpen ? '' : ' is-collapsed' ?>" style="display: <?= $isOpen ? 'block' : 'none' ?>;">
                    <?php foreach ($domainData['clauses'] as $row): ?>
                        <div class="clause-row" id="clause-<?= htmlspecialchars($row['id']) ?>" data-clause-id="<?= htmlspecialchars(strtolower($row['nom'])) ?>" data-clause-num="<?= htmlspecialchars($row['id']) ?>">
                            <div class="clause-header">
                                <div class="clause-code"><?= htmlspecialchars($row['nom']) ?></div>
                                <button
                                    class="clause-favorite-btn"
                                    data-action="favorite-toggle"
                                    data-clause-id="<?= htmlspecialchars($row['id']) ?>"
                                    title="Ajouter aux favoris"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                </button>
                                
                            </div>

                            <!-- ici ont affiche -->
                            <div class="clause-content">
                                <div class="content-section">
                                    <p><?= htmlspecialchars(mb_substr($row['Titre'], 0, 150)) ?>...</p>
                                </div>
                                <div class="content-section">
                                    <div class="content-label">Objective</div>
                                    <p><?= htmlspecialchars(mb_substr($row['Objective'], 0, 150)) ?>...</p>
                                </div>

                                <!-- Section Preuve -->
                                <div class="content-section preuve-section">
                                    <div class="content-label">
                                        Preuves
                                        <?php if (isset($preuvesByClause[$row['id']]) && count($preuvesByClause[$row['id']]) > 0): ?>
                                            <span class="preuves-count"><?= count($preuvesByClause[$row['id']]) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="preuve-container" data-clause-id="<?= htmlspecialchars($row['id']) ?>">
                                        <!-- Liste des preuves existantes -->
                                        <?php if (isset($preuvesByClause[$row['id']]) && !empty($preuvesByClause[$row['id']])): ?>
                                            <div class="preuves-list">
                                                <?php foreach ($preuvesByClause[$row['id']] as $preuve): ?>
                                                    <div class="preuve-item" data-preuve-id="<?= htmlspecialchars($preuve['id']) ?>">
                                                        <a href="<?= htmlspecialchars($preuve['filepath']) ?>" target="_blank" class="preuve-link">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                            </svg>
                                                            <span class="preuve-filename"><?= htmlspecialchars($preuve['filename']) ?></span>
                                                        </a>
                                                        <button type="button" class="preuve-rename-btn" data-action="preuve-rename" data-id="<?= htmlspecialchars($preuve['id']) ?>" title="Renommer">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="preuve-delete-btn" data-action="preuve-delete" data-id="<?= htmlspecialchars($preuve['id']) ?>" title="Supprimer">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Formulaire d'ajout de preuve (toujours visible) -->
                                        <div class="preuve-upload">
                                            <input type="file" class="preuve-input" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" multiple />
                                            <button type="button" class="preuve-upload-btn" data-action="preuve-upload" data-id="<?= htmlspecialchars($row['id']) ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="17 8 12 3 7 8"></polyline>
                                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                                </svg>
                                            </button>
                                            <span class="preuve-add-label">Ajouter des preuves</span>
                                            <?php if (isset($preuvesByClause[$row['id']]) && count($preuvesByClause[$row['id']]) > 0): ?>
                                                <button type="button" class="preuve-clear-all-btn" data-action="preuve-clear" data-id="<?= htmlspecialchars($row['id']) ?>" title="Vider toutes les preuves">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                    Vider tout
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Affichage de toutes les métriques correspondant à cette clause -->
                                <?php if (isset($metricsByClause[$row['id']]) && !empty($metricsByClause[$row['id']])): ?>
                                    <div class="metrics-section">
                                        <div class="content-label">Métriques</div>
                                        <div class="metrics-list">
                                            <?php foreach ($metricsByClause[$row['id']] as $metric): ?>
                                                <div class="metric-item">
                                                    <div class="metric-header">
                                                        <span class="metric-name"><?= htmlspecialchars($metric['nom']) ?></span>
                                                        <div class="metric-info-icon" data-tooltip-content="<?= htmlspecialchars($metric['desc']) ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="10"></circle>
                                                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                            </svg>
                                                            <div class="metric-tooltip"></div>
                                                        </div>
                                                    </div>
                                                    <?php if (isset($valuesByMetric[$metric['metrique_id']]) && !empty($valuesByMetric[$metric['metrique_id']])): ?>
                                                        <?php $lastValue = end($valuesByMetric[$metric['metrique_id']]); ?>
                                                        <?php $yearValues = isset($allYearValues[$metric['metrique_id']]) ? $allYearValues[$metric['metrique_id']] : []; ?>
                                                        <div class="metric-value-editor">
                                                            <span class="metric-date"><?= htmlspecialchars($lastValue['date']) ?></span>
                                                            <div class="metric-input-group">
                                                                <input type="number" class="metric-value-input" value="<?= htmlspecialchars($lastValue['value']) ?>" data-metric-id="<?= htmlspecialchars($metric['metrique_id']) ?>" />
                                                                <button class="metric-save-btn" data-action="metric-update" data-id="<?= htmlspecialchars($metric['metrique_id']) ?>">✓</button>
                                                            </div>
                                                            <div class="metric-chart-container">
                                                                <canvas
                                                                    id="chart-<?= $metric['metrique_id'] ?>"
                                                                    class="metric-chart"
                                                                    data-chart-id="<?= $metric['metrique_id'] ?>"
                                                                    data-chart-data='<?= json_encode($yearValues) ?>'></canvas>
                                                                <script type="application/json" data-chart-id="<?= $metric['metrique_id'] ?>">
                                                                    <?= json_encode($yearValues) ?>
                                                                </script>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Affichage de la dernière observation correspondant à cette clause -->
                                <?php if (isset($observationsByClause[$row['id']]) && !empty($observationsByClause[$row['id']])): ?>
                                    <div class="metrics-section">
                                        <div class="content-label">
                                            Observation
                                            <?php if (count($observationsByClause[$row['id']]) > 1): ?>
                                                <span class="history-badge"><?= count($observationsByClause[$row['id']]) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                        $lastObservation = end($observationsByClause[$row['id']]);
                                        $allObservations = $observationsByClause[$row['id']];
                                        ?>
                                        <div class="observation-editor">
                                            <span class="metric-date"><?= htmlspecialchars($lastObservation['date']) ?></span>
                                            <div class="observation-note-group">
                                                <label class="note-label">Note:</label>
                                                <div class="note-color-picker" data-observation-id="<?= htmlspecialchars($lastObservation['id']) ?>">
                                                    <button type="button" class="note-color-btn note-0 <?= $lastObservation['note'] == 0 ? 'active' : '' ?>" data-value="0" title="0 - Non conforme"></button>
                                                    <button type="button" class="note-color-btn note-1 <?= $lastObservation['note'] == 1 ? 'active' : '' ?>" data-value="1" title="1 - Partiel"></button>
                                                    <button type="button" class="note-color-btn note-2 <?= $lastObservation['note'] == 2 ? 'active' : '' ?>" data-value="2" title="2 - Conforme"></button>
                                                </div>
                                            </div>
                                            <div class="observation-input-group">
                                                <textarea class="observation-input" data-observation-id="<?= htmlspecialchars($lastObservation['id']) ?>"><?= htmlspecialchars($lastObservation['observation']) ?></textarea>
                                                <button class="observation-save-btn" data-action="obs-update" data-id="<?= htmlspecialchars($lastObservation['id']) ?>">✓</button>
                                            </div>
                                        </div>

                                        <!-- Historique des observations -->
                                        <?php if (count($allObservations) > 1): ?>
                                            <div class="observation-history-toggle">
                                                <button class="history-btn" onclick="toggleObservationHistory(this)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="6 9 12 15 18 9"></polyline>
                                                    </svg>
                                                    Voir l'historique
                                                </button>
                                            </div>
                                            <div class="observation-history-container" style="display: none;">
                                                <!-- Timeline visuelle -->
                                                <div class="observation-timeline-chart-wrapper">
                                                    <canvas
                                                        id="timeline-<?= $row['id'] ?>"
                                                        class="observation-timeline-chart"
                                                        data-timeline-id="<?= $row['id'] ?>"
                                                        data-timeline-data='<?= json_encode($allObservations) ?>'></canvas>
                                                    <script type="application/json" data-timeline-id="<?= $row['id'] ?>">
                                                        <?= json_encode($allObservations) ?>
                                                    </script>
                                                </div>
                                                <div class="observation-timeline-legend">
                                                    <div class="legend-row">
                                                        <span class="legend-title">Code couleurs</span>
                                                        <span class="legend-item"><span class="legend-swatch swatch-low"></span>0</span>
                                                        <span class="legend-item"><span class="legend-swatch swatch-mid"></span>1</span>
                                                        <span class="legend-item"><span class="legend-swatch swatch-high"></span>2</span>
                                                    </div>
                                                </div>

                                                <!-- Détails des observations
                                            <div class="observation-history">
                                                <?php
                                                // Afficher les observations dans l'ordre inverse (plus récentes en haut)
                                                $sortedObservations = array_reverse($allObservations);
                                                foreach ($sortedObservations as $index => $obs):
                                                ?>
                                                    <div class="history-item <?= $index === 0 ? 'current' : '' ?>">
                                                        <div class="history-date"><?= htmlspecialchars($obs['date']) ?></div>
                                                        <div class="history-text"><?= htmlspecialchars($obs['observation']) ?></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div> -->
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Bouton retour en haut -->
    <button class="back-to-top" id="backToTop" title="Retour en haut">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    <script src="js/main.js" type="module"></script>
</body>

</html>
