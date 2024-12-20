<?php

if (!isset($_SERVER['HTTP_HX_REQUEST'])) {
    die('Not an HTMX request');
}

use Meilisearch\Client;

require_once __DIR__.'/../vendor/autoload.php';

$env = parse_ini_file(__DIR__.'/../.env');

$client = new Client('https://meilisearch.andrewrhyand.com/server', $env['MEILISEARCH_SEARCH_KEY']);

$index = $client->index('comics');

$query = trim($_GET['query'] ?? '');

$limit = 15;

$offset = $_GET['offset'] ?? 0;
$offset = (int) $offset;

$semantic_ratio = $_GET['semantic_ratio'] ?? 0;
$semantic_ratio = $semantic_ratio > 0 ? $semantic_ratio / 100 : 0;

$response = $index->search($query, [
    'limit' => $limit,
    'offset' => $offset,
    'matchingStrategy' => 'frequency',
    'attributesToHighlight' => ['title'],
    'highlightPreTag' => '<span class="bg-accent text-accent-content">',
    'highlightPostTag' => '</span>',
    'hybrid' => [
        'embedder' => 'openai',
        'semanticRatio' => $semantic_ratio,
    ],
]);

$comics_count = $response->getHitsCount();
$estimated_total_comics = $response->getEstimatedTotalHits();

$comics = array_map(function ($comic) {
    return array_merge($comic, [
        'title_formatted' => $comic['_formatted']['title'],
        'release_date' => !empty($comic['release_date']) ? date('m/d/Y', strtotime($comic['release_date'])) : null
    ]);
}, $response->getHits());
?>

<div class="gap-2 flex items-center justify-center mb-8">
    <?php if ($comics_count): ?>
        <div class="badge badge-accent">
            Estimated Total Matches: <?= $estimated_total_comics ?>
        </div>
    <?php else: ?>
        <div class="badge badge-accent badge-outline">
            No Matches
        </div>
    <?php endif ?>
</div>
<div
    hx-ext="class-tools"
    class="relative">
    <div id="grid" class="gap-x-4 gap-y-8 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
        <?php foreach ($comics as $index => $comic): ?>
            <div
                class="slide-up card bg-base-100 border"
                classes="add in:<?= $index * 50 ?>ms">
                <figure>
                    <img
                        class="aspect-[2/3] duration-1000 object-cover opacity-0 w-full"
                        src="<?= $comic['image_url'] ?>"
                        alt="<?= $comic['title'] ?>"
                        loading="lazy"
                        onload="this.style.opacity='1'"/>
                </figure>
                <div class="card-body justify-between pb-5 pt-4 px-4 space-y-2">
                    <h2 class="card-title text-left text-base">
                        <span><?= $comic['title_formatted'] ?></span>
                    </h2>
                    <div class="space-y-1">
                        <p class="text-xs">Publisher: <span class="font-medium"><?= $comic['publisher'] ?></span></p>
                        <p class="text-xs">Release Date: <span class="font-medium"><?= $comic['release_date'] ?></span></p>
                    </div>
                </div>
                <?php if ($comic === end($comics)): ?>
                    <div
                        hx-get="/search.php?offset=<?= $offset + 10 ?>"
                        hx-include="[name=query], [name=semantic_ratio]"
                        hx-trigger="revealed"
                        hx-select="#grid > div"
                        hx-target="#grid"
                        hx-swap="beforeend"
                        hx-indicator="#infinite-loader">
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
    <div id="infinite-loader" class="htmx-indicator flex justify-center py-8 w-full">
        <span class="loading loading-infinity loading-lg"></span>
    </div>
</div>
