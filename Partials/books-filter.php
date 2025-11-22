<?php
// Get current filters from query string
$filters = [
    'title' => $_GET['filter-title'] ?? null,
    'author' => $_GET['filter-author'] ?? null,
    'language' => $_GET['filter-language'] ?? null,
    'category' => $_GET['filter-category'] ?? null,
];
?>

<form class="filter-form mb-3" method="get" novalidate>
    <input
        id="filter-title"
        name="filter-title"
        class="form-control"
        placeholder="Title"
        value="<?= htmlspecialchars($filters['title'] ?? '') ?>"
    >

    <input
        id="filter-author"
        name="filter-author"
        class="form-control"
        placeholder="Author"
        value="<?= htmlspecialchars($filters['author'] ?? '') ?>"
    >

    <select id="filter-language" name="filter-language" class="form-control">
        <option value="">Language</option>
        <?php
        $languages = \LMS_Website\Enums\LanguageEnum::toArray();
        foreach ($languages as $lang):
            $selected = ($filters['language'] ?? '') === $lang ? 'selected' : '';
            ?>
            <option value="<?= $lang ?>" <?= $selected ?>><?= $lang ?></option>
        <?php endforeach; ?>
    </select>

    <select id="filter-category" name="filter-category" class="form-control">
        <option value="">Category</option>
        <?php
        $categories = \LMS_Website\Enums\CategoryEnum::toArray();
        foreach ($categories as $cat):
            $selected = ($filters['category'] ?? '') === $cat ? 'selected' : '';
            ?>
            <option value="<?= $cat ?>" <?= $selected ?>><?= $cat ?></option>
        <?php endforeach; ?>
    </select>

    <a href="browse.php" class="btn btn-secondary ml-2">
        Reset Filters
    </a>
    <button type="submit" class="btn btn-outline-primary filter-submit">Search</button>


</form>