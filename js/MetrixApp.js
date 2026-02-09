// On importe les classes nécessaires pour éviter les erreurs de type/unused imports
import UpdateManager from './managers/UpdateManager.js';
import Clause from './clauses/Clause.js';
import MetricChart from './charts/MetricChart.js';
import ObservationTimeline from './charts/ObservationTimeline.js';
import PreuveManager from './managers/PreuveManager.js';
import ChartManager from './managers/ChartManager.js';
import DomainManager from './managers/DomainManager.js';

// Classe principale de l'application, qui instancie les managers et gère les interactions globales
export default class MetrixApp {

  // On initialise les managers et les charts, et on écoute les clics sur les éléments interactifs
  constructor() {
    this.updateManager = new UpdateManager();
    this.clause = new Clause();
    this.preuveManager = new PreuveManager();

    // conserver les références pour éviter les erreurs de type/unused imports
    this.MetricChart = new MetricChart();
    this.ObservationTimeline = new ObservationTimeline();
    window.metrixApp = this;

    this.onClick = this.onClick.bind(this);
    document.addEventListener('click', this.onClick);

    this.chartManager = new ChartManager();
    this.initializeVisibleCharts = this.chartManager.initializeVisibleCharts.bind(this.chartManager);
    this.initializeCharts = this.chartManager.initializeCharts.bind(this.chartManager);
    this.initializeVisibleCharts();

    this.domainManager = new DomainManager(this.initializeCharts);
  }

  onClick(e) {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;

    const action = btn.dataset.action;
    const id = btn.dataset.id;

    switch (action) {
      case 'metric-update': return this.updateManager.updateMetricValue(btn, id);
      case 'obs-update': return this.updateManager.updateObservation(btn, id);
      case 'form-submit': return this.handleFormSubmit(btn);
      case 'clause-add': return this.handleFormSubmit(btn, '../api/add_clause.php', 'clause');
      case 'domaine-add': return this.handleFormSubmit(btn, '../api/add_domaine.php', 'domaine');

      case 'domain-toggle': return this.domainManager.toggleDomainSection(btn);
      case 'sidebar-domain-toggle': return window.toggleSidebarDomain?.(btn);
      case 'favorite-toggle': return window.toggleFavorite?.(e, btn.dataset.clauseId);
      case 'preuve-upload': return this.preuveManager.uploadPreuve(btn, id);
      case 'preuve-delete': return this.preuveManager.deletePreuve(btn, id);
      case 'preuve-rename': return this.preuveManager.renamePreuve(btn, id);
      case 'preuve-clear': return this.preuveManager.deleteAllPreuves(btn, id);
    }
  }

  handleFormSubmit(btn, fallbackEndpoint = null, fallbackEntity = null) {
    const formSelector = btn.dataset.form;
    const endpoint = btn.dataset.endpoint || fallbackEndpoint;
    const entity = btn.dataset.entity || fallbackEntity;

    if (!formSelector || !endpoint) {
      alert('Configuration du formulaire invalide');
      return;
    }

    const form = document.querySelector(formSelector);
    if (!form) {
      alert('Formulaire introuvable');
      return;
    }

    const data = Object.fromEntries(new FormData(form).entries());
    if (entity && !data.entity) data.entity = entity;

    return this.clause.sendInsert(endpoint, data, btn);
  }
}
