/**
 * Soba Zero-Waste — Universal Interactive Popups & Notifications
 * Replaces default browser alert() and confirm() with custom glassmorphism modal dialogs.
 */
(function(window) {
  'use strict';

  // Ensure DOM container for Soba modals
  function ensureModalContainer() {
    let container = document.getElementById('soba-modal-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'soba-modal-container';
      container.innerHTML = `
        <div id="sobaGlobalToast" class="soba-global-toast">
          <span id="sobaToastIcon">✓</span>
          <span id="sobaToastMsg">Message</span>
        </div>

        <div id="sobaConfirmModal" class="soba-modal-backdrop">
          <div class="soba-modal-box">
            <div class="soba-modal-icon warning" id="sobaModalIcon">⚠️</div>
            <h3 class="soba-modal-title" id="sobaModalTitle">Confirmation</h3>
            <p class="soba-modal-msg" id="sobaModalMsg">Are you sure?</p>
            <div class="soba-modal-actions" id="sobaModalActions">
              <button type="button" class="soba-btn soba-btn-cancel" id="sobaModalCancel">Cancel</button>
              <button type="button" class="soba-btn soba-btn-confirm" id="sobaModalConfirm">OK</button>
            </div>
          </div>
        </div>
      `;
      document.body.appendChild(container);
    }
    return container;
  }

  // Toast Functionality
  let toastTimer = null;
  window.sobaToast = function(msg, isError = false) {
    ensureModalContainer();
    const toast = document.getElementById('sobaGlobalToast');
    const msgEl = document.getElementById('sobaToastMsg');
    const iconEl = document.getElementById('sobaToastIcon');
    if (!toast) return;

    msgEl.textContent = msg;
    iconEl.textContent = isError ? '✕' : '✓';
    toast.className = 'soba-global-toast' + (isError ? ' error' : '');
    
    // Trigger animation
    void toast.offsetWidth;
    toast.classList.add('show');

    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      toast.classList.remove('show');
    }, 3200);
  };

  // Promise-based custom Confirm Modal
  window.sobaConfirm = function(msg, title = "Please Confirm", isDanger = true) {
    return new Promise((resolve) => {
      ensureModalContainer();
      const modal = document.getElementById('sobaConfirmModal');
      const titleEl = document.getElementById('sobaModalTitle');
      const msgEl = document.getElementById('sobaModalMsg');
      const iconEl = document.getElementById('sobaModalIcon');
      const cancelBtn = document.getElementById('sobaModalCancel');
      const confirmBtn = document.getElementById('sobaModalConfirm');

      titleEl.textContent = title;
      msgEl.textContent = msg;
      iconEl.className = 'soba-modal-icon ' + (isDanger ? 'warning' : 'info');
      iconEl.textContent = isDanger ? '⚠️' : 'ℹ️';

      confirmBtn.className = 'soba-btn soba-btn-confirm' + (isDanger ? ' danger' : '');
      confirmBtn.textContent = isDanger ? 'Yes, Proceed' : 'OK';
      cancelBtn.style.display = 'inline-block';

      function cleanUp() {
        modal.classList.remove('active');
        cancelBtn.removeEventListener('click', onCancel);
        confirmBtn.removeEventListener('click', onConfirm);
      }

      function onCancel() {
        cleanUp();
        resolve(false);
      }

      function onConfirm() {
        cleanUp();
        resolve(true);
      }

      cancelBtn.addEventListener('click', onCancel);
      confirmBtn.addEventListener('click', onConfirm);

      modal.classList.add('active');
    });
  };

  // Custom Alert Modal
  window.sobaAlert = function(msg, title = "Notification") {
    return new Promise((resolve) => {
      ensureModalContainer();
      const modal = document.getElementById('sobaConfirmModal');
      const titleEl = document.getElementById('sobaModalTitle');
      const msgEl = document.getElementById('sobaModalMsg');
      const iconEl = document.getElementById('sobaModalIcon');
      const cancelBtn = document.getElementById('sobaModalCancel');
      const confirmBtn = document.getElementById('sobaModalConfirm');

      titleEl.textContent = title;
      msgEl.textContent = msg;
      iconEl.className = 'soba-modal-icon success';
      iconEl.textContent = '✨';

      cancelBtn.style.display = 'none';
      confirmBtn.className = 'soba-btn soba-btn-confirm';
      confirmBtn.textContent = 'Got it';

      function onConfirm() {
        modal.classList.remove('active');
        confirmBtn.removeEventListener('click', onConfirm);
        resolve(true);
      }

      confirmBtn.addEventListener('click', onConfirm);
      modal.classList.add('active');
    });
  };

  // Override native browser alert with sobaToast or custom modal
  window.alert = function(msg) {
    if (typeof msg === 'string' && (msg.toLowerCase().includes('error') || msg.toLowerCase().includes('failed') || msg.toLowerCase().includes('invalid'))) {
      window.sobaToast(msg, true);
    } else {
      window.sobaToast(msg, false);
    }
  };

})(window);
