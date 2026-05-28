<?php

/**
 * Bootstrap 5 pagination template for CodeIgniter 4 Pager.
 *
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<nav aria-label="Page navigation">
    <ul class="pagination pagination-sm justify-content-center flex-wrap">

        <!-- First / Prev -->
        <?php if ($pager->hasPreviousPage()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="First">«</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="Previous">‹</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled"><span class="page-link">«</span></li>
            <li class="page-item disabled"><span class="page-link">‹</span></li>
        <?php endif; ?>

        <!-- Numbered pages -->
        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach; ?>

        <!-- Next / Last -->
        <?php if ($pager->hasNextPage()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="Next">›</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Last">»</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled"><span class="page-link">›</span></li>
            <li class="page-item disabled"><span class="page-link">»</span></li>
        <?php endif; ?>

    </ul>
</nav>
