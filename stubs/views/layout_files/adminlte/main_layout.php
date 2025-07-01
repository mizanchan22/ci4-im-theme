    <?= $this->include('layouts/top_layout') ?>

    <body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
        <!--begin::App Wrapper-->
        <div class="app-wrapper">
            <?= $this->include('layouts/sidebar_layout') ?>

            <?= $this->renderSection('app-wrapper') ?>

        </div>

        <?= $this->include('layouts/bottom_layout') ?>
    </body>