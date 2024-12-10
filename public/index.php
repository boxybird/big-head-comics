<!DOCTYPE html>
<html lang="en" data-theme="cyberpunk" class="!overflow-y-scroll">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Big Head Comics</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

        <!-- DaisyUI -->
        <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" type="text/css"/>

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
          tailwind.config = {
            theme: {
              fontFamily: {
                sans: ['Open Sans', 'sans-serif'],
              },
            },
          }
        </script>

        <!-- Alpine Plugins -->
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
        <!-- Alpine JS -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- HTMX -->
        <script src="https://unpkg.com/htmx.org@2.0.3"></script>
        <!-- HTMX Extensions -->
        <script src="https://unpkg.com/htmx-ext-class-tools@2.0.1/class-tools.js"></script>

        <!-- Custom CSS -->
        <style>
            .htmx-indicator {
                opacity: 0;
                transition: opacity 500ms ease-in;
            }

            .htmx-request .htmx-indicator {
                opacity: 1;
            }

            .htmx-request.htmx-indicator {
                opacity: 1;
            }

            [x-cloak] {
                display: none;
            }

            .slide-up {
                opacity: 0;
                transform: translateY(1rem);
                transition: all 300ms ease-out;
            }

            .slide-up.in {
                opacity: 1;
                transform: translateY(0);
            }
        </style>
    </head>
    <body
        x-data="{
            theme: $persist('cyberpunk'),
            semanticRatio: 0
        }"
        x-effect="document.documentElement.setAttribute('data-theme', theme)"
        x-cloak
        hx-get="/search.php"
        hx-trigger="load"
        hx-target="#results"
        hx-swap="innerHTML"
        hx-indicator="#loader">
        <main>
            <div class="drawer drawer-end lg:drawer-open">
                <input id="drawer" type="checkbox" class="drawer-toggle" />
                <div class="drawer-content px-4 lg:px-8">
                    <div class="navbar bg-base-300 max-w-4xl mt-4 mx-auto rounded-box shadow lg:mt-12">
                        <div class="navbar-start"></div>
                        <div class="navbar-center">
                            <a class="btn btn-ghost text-xl" href="/">Big &lt;/Head&gt; Comics</a>
                        </div>
                        <div class="navbar-end">
                            <label for="drawer" class="cursor-pointer lg:hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                                </svg>
                            </label>
                        </div>
                    </div>
                    <form
                        hx-get="/search.php"
                        hx-trigger="submit, keyup changed delay:300ms"
                        hx-target="#results"
                        hx-swap="innerHTML"
                        hx-indicator="#loader"
                        class="gap-x-8 gap-y-8 flex flex-col items-end justify-between max-w-3xl mt-8 mx-auto sm:flex-row">
                        <div class="gap-x-8 gap-y-3 flex flex-1 flex-col w-full sm:flex-row">
                            <label class="form-control flex-1 w-full">
                                <div class="label">
                                    <span class="label-text">Comic Query</span>
                                </div>
                                <input type="text" name="query" class="input input-bordered" value="<?= $_GET['query'] ?? '' ?>" placeholder="Search..." required autofocus />
                            </label>
                            <!-- <label class="form-control w-full sm:w-52">
                                <div class="label">
                                    <span class="label-text">Semantic Ratio: <span x-text="`${semanticRatio}%`" class="font-bold"></span></span>
                                </div>
                                <input x-model="semanticRatio" type="range" name="semantic_ratio" min="0" max="100" step="25" value="0" class="range range-sm mt-2" />
                                <div class="flex w-full justify-between px-2 text-xs"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div> -->
                            </label>
                        </div>
                        <button class="btn _btn-lg btn-primary w-full sm:w-auto">Search</button>
                    </form>
                    <div class="relative">
                        <div id="loader" class="htmx-indicator absolute flex inset-0 justify-center w-full">
                            <span class="loading loading-ring loading-lg"></span>
                        </div>
                        <div class="divider py-10">RESULTS</div>
                    </div>
                    <!-- HTMX Output -->
                    <div id="results" class="max-w-5xl mx-auto"></div>
                    <!-- /Page content -->
                </div>
                <div class="drawer-side">
                    <label for="drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                    <div class="bg-base-200 gap-2 flex flex-col min-h-screen p-4 text-base-content w-40">
                        <span class="font-semibold text-center text-lg">Themes</span>
                        <?php foreach (['light', 'dark', 'cupcake', 'bumblebee', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'garden', 'forest', 'aqua', 'luxury', 'dracula', 'night', 'coffee'] as $theme): ?>
                            <button
                                @click="theme = '<?= $theme ?>'"
                                :class="{ 'btn-active': theme === '<?= $theme ?>' }"
                                class="btn btn-outline btn-sm">
                                <?= ucfirst($theme) ?>
                            </button>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
