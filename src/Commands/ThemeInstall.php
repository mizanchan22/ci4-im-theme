<?php

namespace IM1\Installer\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ThemeInstall extends BaseCommand
{
    protected $group       = 'im1';
    protected $name        = 'im1:theme';
    protected $description = 'Interactive installer to switch CodeIgniter 4 themes';

    public function run(array $params)
    {
        CLI::newLine();
        CLI::write(str_repeat('*', 100), 'light_green');
        CLI::write(str_pad('IM1 R&D Composer CI4 Themes', 100, ' ', STR_PAD_BOTH), 'yellow');
        CLI::write(str_repeat('*', 100), 'light_green');
        CLI::newLine();

        CLI::write("1. Bootstrap 5", 'white');
        CLI::write("2. Bootstrap 4", 'white');
        CLI::write("3. Bootstrap 3", 'white');
        CLI::newLine();

        $version = CLI::prompt("Pilih versi Bootstrap (1-3)");

        if ($version === '1') {
            $this->selectBootstrap5();
        } else {
            CLI::error("❌ Versi belum disokong.");
        }
    }

    protected function selectBootstrap5(): void
    {
        CLI::newLine();
        CLI::write("Tema Bootstrap 5:", 'cyan');
        CLI::write("1. AdminLTE (on progress)", 'white');
        CLI::write("2. NiceAdmin (soon)", 'white');
        CLI::write("3. Sneat (on progress)", 'white');
        CLI::newLine();

        $choice = CLI::prompt("Pilih tema (1-2)");

        $themeMap = [
            '1' => 'adminlte',
            '2' => 'niceadmin',
            '3' => 'sneat',
        ];

        if (!isset($themeMap[$choice])) {
            CLI::error("❌ Pilihan tidak sah.");
            return;
        }

        $themeKey = $themeMap[$choice];
        $this->copyTheme($themeKey);
    }

    protected function copyTheme(string $themeKey): void
    {
        $vendorPath    = ROOTPATH . 'vendor/mizanchan22/ci4-im-theme/stubs/';
        $sourceViews   = $vendorPath . "views/layout_files/" . $themeKey;
        $sourceAssetsCSS  = $vendorPath . "public/assets/" . $themeKey . "/css";
        $sourceAssetsJS  = $vendorPath . "public/assets/" . $themeKey . "/js";

        $targetViews   = APPPATH . 'Views/layouts/';
        $targetAssetsCSS  = FCPATH . 'assets/css/';
        $targetAssetsJS  = FCPATH . 'assets/js/';

        $this->recreateFolder($targetViews);
        $this->recreateFolder($targetAssetsCSS);
        $this->recreateFolder($targetAssetsJS);

        $this->copyDirectory($sourceViews, $targetViews);
        $this->copyDirectory($sourceAssetsCSS, $targetAssetsCSS);
        $this->copyDirectory($sourceAssetsJS, $targetAssetsJS);

        $this->updateBaseController();
        $this->replaceWelcomeMessage();


        CLI::newLine();
        CLI::write("✅ Tema '$themeKey' telah dipasang ke projek CI4 anda.", 'green');
        CLI::newLine();
        CLI::write("📁 Layouts: " . realpath($targetViews), 'blue');
        CLI::write("📁 Assets CSS:  " . realpath($targetAssetsCSS), 'blue');
        CLI::write("📁 Assets JS:  " . realpath($targetAssetsJS), 'blue');
        CLI::newLine(2);
    }

    protected function recreateFolder(string $path): void
    {
        if (is_dir($path)) {
            $this->deleteDirectory($path);
        }
        mkdir($path, 0755, true);
    }

    protected function copyDirectory(string $src, string $dst): void
    {
        if (!is_dir($src)) {
            CLI::error("❌ Folder tidak wujud: $src");
            return;
        }

        $dir = opendir($src);
        @mkdir($dst, 0755, true);

        while (false !== ($file = readdir($dir))) {
            if ($file !== '.' && $file !== '..') {
                $srcPath = "$src/$file";
                $dstPath = "$dst/$file";

                if (is_dir($srcPath)) {
                    $this->copyDirectory($srcPath, $dstPath);
                } else {
                    copy($srcPath, $dstPath);
                }
            }
        }

        closedir($dir);
    }

    protected function deleteDirectory(string $dir): void
    {
        if (!file_exists($dir)) return;

        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = "$dir/$item";
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    protected function updateBaseController(): void
    {
        $file = APPPATH . 'Controllers/BaseController.php';

        if (!file_exists($file)) {
            CLI::error("❌ BaseController.php tidak dijumpai.");
            return;
        }

        $original = "protected \$helpers = [];";
        $replacement = "protected \$helpers = ['html', 'form', 'date'];";

        $content = file_get_contents($file);

        if (strpos($content, $replacement) !== false) {
            CLI::write("ℹ️  BaseController.php sudah dikemaskini sebelum ini.", 'yellow');
            return;
        }

        if (strpos($content, $original) === false) {
            CLI::error("❌ Gagal cari line helpers dalam BaseController.php");
            return;
        }

        $updated = str_replace($original, $replacement, $content);
        file_put_contents($file, $updated);

        CLI::write("✅ BaseController.php dikemaskini dengan helper ['html', 'form', 'date']", 'green');
    }


        protected function replaceWelcomeMessage(): void
        {
            $file = APPPATH . 'Views/welcome_message.php';

            if (!file_exists($file)) {
                CLI::error("❌ welcome_message.php tidak dijumpai.");
                return;
            }

            $newContent = <<<PHP
        <?= \$this->extend('layouts/main_layout') ?>
<?= \$this->section('app-wrapper') ?>

<!-- app wrapper -->
<div class="app-wrapper">
    <!-- Content -->
    DaH JADI INStall themes
    <!-- / Content -->
</div>
<!-- Content wrapper -->

<?= \$this->endSection() ?>
PHP;

file_put_contents($file, $newContent);
CLI::write("✅ welcome_message.php digantikan dengan template layout.", 'green');
}

}