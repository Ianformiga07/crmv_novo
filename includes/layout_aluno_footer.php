    </div><!-- .aluno-container -->
</main><!-- .aluno-main -->

<script>
// ── Menu mobile aluno ─────────────────────────────────────
document.getElementById('alunoMenuBtn')?.addEventListener('click', () => {
    document.getElementById('alunoNav').classList.toggle('aberta');
});

// ── Auto-dismiss flash ────────────────────────────────────
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
