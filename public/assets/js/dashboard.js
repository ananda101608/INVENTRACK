/* =====================================================================
   INVENTRACK — dashboard.js
   Tooltip ringan untuk bar chart CSS
   ===================================================================== */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    const chart = document.querySelector('[data-bar-chart]');
    if (!chart) return;

    const tooltip = document.createElement('div');
    tooltip.className = 'pointer-events-none fixed z-[70] hidden rounded-lg bg-slate-900 px-2.5 py-1.5 ' +
      'text-[11px] font-medium text-white shadow-xl';
    document.body.appendChild(tooltip);

    chart.querySelectorAll('[data-bar]').forEach((bar) => {
      bar.addEventListener('mouseenter', () => {
        tooltip.textContent = bar.getAttribute('data-label') || '';
        tooltip.classList.remove('hidden');
      });

      bar.addEventListener('mousemove', (event) => {
        tooltip.style.left = event.clientX + 12 + 'px';
        tooltip.style.top = event.clientY - 34 + 'px';
      });

      bar.addEventListener('mouseleave', () => {
        tooltip.classList.add('hidden');
      });
    });
  });
})();
