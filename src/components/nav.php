
<nav style="display: flex; width: 100%; justify-content: space-between; align-items: center;">
    <div style="width: max-content">
        <a href="/libero" class="fly-heading fly-link-heading">flighty</a>
        <div class="fly-margin" style="margin-top: 0; margin-bottom: 0;">computer science and such</div>
    </div>
    <ul class="fly-nav fly-margin">
        <?php foreach($pages as $page): ?>
            <li><a href="/<?php $page["id"] ?>"><?php $page["title"] ?></a></li>
        <?php endforeach ?>
    </ul>
</nav>
