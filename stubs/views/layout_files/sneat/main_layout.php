    <?= $this->include('layouts/top_layout') ?>

    <body>
        <!--begin::App Wrapper-->
        <!-- app wrapper -->
        <div class="app-wrapper">

            <?= $this->include('layouts/sidebar_layout') ?>

            <?= $this->renderSection('app-wrapper') ?>

        </div>

        <?= $this->include('layouts/bottom_layout') ?>
    </body>