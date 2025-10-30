<ul>
    <?php foreach ($links as $act => $link): ?>
        <li><a href="<?= $link['url'] ?>"><?= $act == 'kill' ? 'Удаление': 'Редактирование' ?></a> Будет доступна до <?= $link['until']?></li>
    <?php endforeach ?>
</ul>