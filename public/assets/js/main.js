/* =====================================================================
   INVENTRACK — main.js
   Interaksi global: sidebar, dropdown, flash, modal, tabel, repeater
   ===================================================================== */
(function () {
  'use strict';

  const $  = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  /* ---------------------------------------------------------------
     Sidebar (mobile)
     --------------------------------------------------------------- */
  function initSidebar() {
    const sidebar = $('#sidebar');
    const overlay = $('#sidebar-overlay');
    if (!sidebar) return;

    const open = () => {
      sidebar.classList.remove('-translate-x-full');
      overlay?.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    };
    const close = () => {
      sidebar.classList.add('-translate-x-full');
      overlay?.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    };

    $('#sidebar-open')?.addEventListener('click', open);
    $('#sidebar-close')?.addEventListener('click', close);
    overlay?.addEventListener('click', close);

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });
  }

  /* ---------------------------------------------------------------
     Dropdown user
     --------------------------------------------------------------- */
  function initDropdown() {
    const button = $('#user-menu-button');
    const menu   = $('#user-menu');
    if (!button || !menu) return;

    button.addEventListener('click', (e) => {
      e.stopPropagation();
      menu.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!menu.contains(e.target) && !button.contains(e.target)) {
        menu.classList.add('hidden');
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') menu.classList.add('hidden');
    });
  }

  /* ---------------------------------------------------------------
     Flash / toast
     --------------------------------------------------------------- */
  function initFlash() {
    $$('[data-flash]').forEach((flash) => {
      const dismiss = () => {
        flash.classList.add('opacity-0', 'translate-y-[-8px]');
        flash.style.transition = 'opacity .25s ease, transform .25s ease';
        setTimeout(() => flash.remove(), 260);
      };

      flash.querySelector('[data-flash-close]')?.addEventListener('click', dismiss);
      setTimeout(dismiss, 6000);
    });
  }

  /* ---------------------------------------------------------------
     Konfirmasi aksi destruktif
     --------------------------------------------------------------- */
  function initConfirm() {
    document.addEventListener('click', (e) => {
      const el = e.target.closest('[data-confirm]');
      if (!el) return;

      if (!window.confirm(el.getAttribute('data-confirm') || 'Yakin melanjutkan?')) {
        e.preventDefault();
        e.stopImmediatePropagation();
      }
    });
  }

  /* ---------------------------------------------------------------
     Pencarian tabel client-side
     --------------------------------------------------------------- */
  function initTableSearch() {
    $$('[data-table-search]').forEach((input) => {
      const table = document.getElementById(input.getAttribute('data-table-search'));
      if (!table) return;

      const rows = $$('tbody tr', table).filter((tr) =>
        !tr.hasAttribute('data-empty-row') && !tr.hasAttribute('data-search-empty')
      );
      const empty = $('[data-search-empty]', input.closest('[data-table-wrap]') || document);
      const counter = $('[data-table-count]', input.closest('[data-table-wrap]') || document);

      const apply = () => {
        const q = input.value.trim().toLowerCase();
        let visible = 0;

        rows.forEach((row) => {
          const match = !q || row.textContent.toLowerCase().includes(q);
          row.classList.toggle('hidden', !match);
          if (match) visible++;
        });

        if (empty) empty.classList.toggle('hidden', !(q !== '' && visible === 0));
        if (counter) counter.textContent = visible;

        const emptyRow = $('[data-empty-row]', table);
        if (emptyRow) emptyRow.classList.toggle('hidden', q !== '');
      };

      input.addEventListener('input', apply);
      apply();
    });
  }

  /* ---------------------------------------------------------------
     Modal <dialog> generik
     - data-modal-open="id-dialog"
     - data-modal-action="url"  -> mengganti action form
     - data-reset="1"           -> kosongkan form saat dibuka
     - data-fill='{"key":...}'  -> isi field sesuai name
     --------------------------------------------------------------- */
  function initModals() {
    document.addEventListener('click', (e) => {
      const opener = e.target.closest('[data-modal-open]');
      if (!opener) return;

      const dialog = document.getElementById(opener.getAttribute('data-modal-open'));
      if (!dialog) return;

      const form = $('form', dialog);

      if (form) {
        if (opener.dataset.modalAction) form.setAttribute('action', opener.dataset.modalAction);

        if (opener.dataset.reset) {
          form.reset();
          const title = $('[data-modal-title]', dialog);
          if (title && opener.dataset.modalTitle) title.textContent = opener.dataset.modalTitle;
        }

        if (opener.dataset.fill) {
          try {
            const data = JSON.parse(opener.dataset.fill);
            Object.entries(data).forEach(([key, value]) => {
              const field = form.elements.namedItem(key);
              if (!field) return;
              if (field instanceof RadioNodeList) {
                field.value = value;
              } else {
                field.value = value ?? '';
              }
            });
            const title = $('[data-modal-title]', dialog);
            if (title && opener.dataset.modalTitle) title.textContent = opener.dataset.modalTitle;
          } catch (err) {
            console.warn('Data modal tidak valid', err);
          }
        }
      }

      dialog.showModal();
    });

    document.addEventListener('click', (e) => {
      const closer = e.target.closest('[data-modal-close]');
      if (closer) {
        closer.closest('dialog')?.close();
        return;
      }
      // klik backdrop
      if (e.target instanceof HTMLDialogElement) {
        e.target.close();
      }
    });
  }

  /* ---------------------------------------------------------------
     Repeater item transaksi
     --------------------------------------------------------------- */
  function initRepeaters() {
    $$('[data-repeater]').forEach((root) => {
      const rowsBox  = $('[data-repeater-rows]', root);
      const template = $('[data-repeater-template]', root);
      const addBtn   = $('[data-repeater-add]', root);
      if (!rowsBox || !template) return;

      const rowStock = (row) => {
        const select = $('select[data-product-select]', row);
        if (select && select.selectedOptions[0]) {
          const fromOption = parseFloat(select.selectedOptions[0].dataset.stock || '0') || 0;
          if (select.value !== '') return fromOption;
        }
        return parseFloat($('input[data-stock]', row)?.textContent || '0') || 0;
      };

      const recalc = () => {
        let grand = 0;
        let grandQty = 0;

        $$('[data-repeater-row]', rowsBox).forEach((row, index) => {
          const qty   = parseFloat($('input[data-qty]', row)?.value || '0') || 0;
          const price = parseFloat($('input[data-price]', row)?.value || '0') || 0;
          const total = qty * price;
          grand += total;
          grandQty += qty;

          const line = $('[data-line-total]', row);
          if (line) {
            line.textContent = price > 0
              ? 'Rp ' + total.toLocaleString('id-ID')
              : String(qty);
          }

          const stock = rowStock(row);
          const qtyInput = $('input[data-qty]', row);
          if (qtyInput && selectHasValue(row) && stock > 0 && qty > stock) {
            qtyInput.classList.add('border-rose-400', 'ring-2', 'ring-rose-100');
          } else if (qtyInput) {
            qtyInput.classList.remove('border-rose-400', 'ring-2', 'ring-rose-100');
          }

          const rowNumber = $('[data-row-number]', row);
          if (rowNumber) rowNumber.textContent = String(index + 1);
        });

        $$('[data-grand-total]', root).forEach((el) => {
          el.textContent = 'Rp ' + grand.toLocaleString('id-ID');
        });

        $$('[data-grand-qty]', root).forEach((el) => {
          el.textContent = grandQty.toLocaleString('id-ID');
        });
      };

      const selectHasValue = (row) => {
        const select = $('select[data-product-select]', row);
        return !select || select.value !== '';
      };

      const fillFromProduct = (row) => {
        const select = $('select[data-product-select]', row);
        if (!select) return;

        const option = select.selectedOptions[0];
        const priceField = $('input[data-price]', row);
        if (priceField && option?.dataset.price !== undefined) {
          priceField.value = option.dataset.price;
        }

        const stockLabel = $('[data-stock-label]', row);
        if (stockLabel) {
          stockLabel.textContent = option?.dataset.stock !== undefined
            ? option.dataset.stock + (option.dataset.unit ? ' ' + option.dataset.unit : '')
            : '—';
        }
      };

      const bindRow = (row) => {
        $$('input, select', row).forEach((el) => {
          el.addEventListener('input', recalc);
          el.addEventListener('change', () => {
            if (el.matches('[data-product-select]')) fillFromProduct(row);
            recalc();
          });
        });
        $('[data-repeater-remove]', row)?.addEventListener('click', () => {
          if ($$('[data-repeater-row]', rowsBox).length <= 1) {
            $$('input, select', row).forEach((el) => {
              if (el.matches('[data-qty], [data-price]')) el.value = '';
            });
          } else {
            row.remove();
          }
          recalc();
        });
      };

      $$('[data-repeater-row]', rowsBox).forEach(bindRow);

      addBtn?.addEventListener('click', () => {
        const fragment = template.content.cloneNode(true);
        const row = fragment.querySelector('[data-repeater-row]');
        rowsBox.appendChild(fragment);
        if (row) {
          bindRow(row);
          $('input[data-qty]', row)?.focus();
        }
        recalc();
      });

      recalc();
    });
  }

  /* ---------------------------------------------------------------
     Stok opname: hitung selisih fisik vs sistem
     --------------------------------------------------------------- */
  function initOpname() {
    $$('[data-opname-table]').forEach((table) => {
      const recalc = () => {
        let plus = 0;
        let minus = 0;
        let checked = 0;

        $$('[data-opname-row]', table).forEach((row) => {
          const system = parseInt($('[data-system]', row)?.textContent || '0', 10) || 0;
          const input  = $('[data-physical]', row);
          if (!input) return;

          const physical = parseInt(input.value || '0', 10) || 0;
          const diff = physical - system;
          const cell = $('[data-diff]', row);

          if (cell) {
            cell.textContent = (diff > 0 ? '+' : '') + diff;
            cell.className = 'px-3 py-2 text-right text-sm font-semibold ' +
              (diff === 0 ? 'text-slate-400' : diff > 0 ? 'text-emerald-600' : 'text-rose-600');
          }

          if (diff > 0) plus += diff;
          if (diff < 0) minus += Math.abs(diff);
          checked++;
        });

        const plusEl = $('[data-total-plus]');
        const minusEl = $('[data-total-minus]');
        const countEl = $('[data-total-checked]');
        if (plusEl) plusEl.textContent = plus > 0 ? '+' + plus : '0';
        if (minusEl) minusEl.textContent = minus > 0 ? '-' + minus : '0';
        if (countEl) countEl.textContent = String(checked);
      };

      $$('input[data-physical]', table).forEach((input) => {
        input.addEventListener('input', recalc);
      });

      recalc();
    });
  }

  /* ---------------------------------------------------------------
     Form: normalisasi datetime-local + konfirmasi submit
     --------------------------------------------------------------- */
  function initForms() {
    $$('form').forEach((form) => {
      form.addEventListener('submit', () => {
        $$('input[type="datetime-local"]', form).forEach((input) => {
          if (input.value.includes('T')) input.value = input.value.replace('T', ' ') + ':00';
        });

        const submit = $('[data-submit-guard]', form);
        if (submit) {
          submit.disabled = true;
          submit.classList.add('opacity-70');
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initDropdown();
    initFlash();
    initConfirm();
    initTableSearch();
    initModals();
    initRepeaters();
    initOpname();
    initForms();
  });
})();
