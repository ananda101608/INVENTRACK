<?php
// ====================================================================
// PARTIAL: FLASH MESSAGE (toast)
// ====================================================================
$flash = get_flash();

if ($flash):
    $map = [
        'success' => ['icon' => 'check',   'wrap' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'iconColor' => 'text-emerald-600'],
        'error'   => ['icon' => 'alert',   'wrap' => 'border-rose-200 bg-rose-50 text-rose-800',          'iconColor' => 'text-rose-600'],
        'warning' => ['icon' => 'alert',   'wrap' => 'border-amber-200 bg-amber-50 text-amber-800',        'iconColor' => 'text-amber-600'],
        'info'    => ['icon' => 'info',    'wrap' => 'border-sky-200 bg-sky-50 text-sky-800',              'iconColor' => 'text-sky-600'],
    ];
    $type = $map[$flash['tipe']] ?? $map['info'];
    require_once __DIR__ . '/icon.php';
    ?>
    <div data-flash
         class="fixed right-4 top-20 z-[60] flex w-[calc(100vw-2rem)] max-w-sm items-start gap-3 rounded-xl border px-4 py-3 shadow-lg <?= $type['wrap'] ?>">
        <span class="mt-0.5 shrink-0 <?= $type['iconColor'] ?>"><?= ui_icon($type['icon'], 'w-5 h-5') ?></span>
        <p class="flex-1 text-sm font-medium leading-relaxed"><?= htmlspecialchars($flash['pesan']) ?></p>
        <button type="button" data-flash-close class="shrink-0 rounded p-0.5 opacity-60 transition hover:opacity-100" aria-label="Tutup">
            <?= ui_icon('close', 'w-4 h-4') ?>
        </button>
    </div>
<?php endif; ?>
