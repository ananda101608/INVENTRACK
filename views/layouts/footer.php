<?php
// ====================================================================
// LAYOUT: FOOTER
// ====================================================================
$footerPage = $_GET['page'] ?? 'dashboard';
?>
        <footer class="mt-10 flex flex-col items-center justify-between gap-2 border-t border-slate-200 pt-5 text-xs text-slate-400 sm:flex-row print:hidden">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(APP_NAME) ?>. <?= htmlspecialchars(APP_TAGLINE) ?></p>
            <p>Versi <?= htmlspecialchars(APP_VERSION) ?></p>
        </footer>
    </main>
</div><!-- /lg:pl-64 -->
</div><!-- /#app -->

<script src="<?= url('assets/js/main.js?v=' . APP_VERSION) ?>"></script>
<?php if ($footerPage === 'dashboard'): ?>
    <script src="<?= url('assets/js/dashboard.js?v=' . APP_VERSION) ?>"></script>
<?php endif; ?>
</body>
</html>
