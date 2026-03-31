    </main><!-- .page-content -->
</div><!-- .main-wrapper -->

<script>
// ── Sidebar toggle (mobile) ──────────────────────────────
const sidebar = document.getElementById('sidebar');
document.getElementById('btnSidebar')?.addEventListener('click', () => {
    sidebar.classList.toggle('aberta');
});
document.addEventListener('click', (e) => {
    if (window.innerWidth < 900 &&
        sidebar.classList.contains('aberta') &&
        !sidebar.contains(e.target) &&
        !document.getElementById('btnSidebar').contains(e.target)) {
        sidebar.classList.remove('aberta');
    }
});

// ── Auto-dismiss flash messages ───────────────────────────
setTimeout(() => {
    document.querySelectorAll('.flash').forEach(el => {
        el.style.transition = 'opacity .4s';
        el.style.opacity    = '0';
        setTimeout(() => el.remove(), 400);
    });
}, 5000);
</script>
<?php if (!empty($extraJs)): ?>
<script><?= $extraJs ?></script>
<?php endif; ?>
</body>
</html>
