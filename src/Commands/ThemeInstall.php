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
        CLI::write("1. AdminLTE", 'white');
        CLI::write("2. NiceAdmin", 'white');
        CLI::newLine();

        $choice = CLI::prompt("Pilih tema (1-2)");

        $themeMap = [
            '1' => 'adminlte',
            '2' => 'niceadmin',
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
        $sourceAssets  = $vendorPath . "public/assets/" . $themeKey . "/css";

        $targetViews   = APPPATH . 'Views/layouts/';
        $targetAssets  = FCPATH . 'assets/css/';

        $this->recreateFolder($targetViews);
        $this->recreateFolder($targetAssets);

        $this->copyDirectory($sourceViews, $targetViews);
        $this->copyDirectory($sourceAssets, $targetAssets);

        CLI::newLine();
        CLI::write("✅ Tema '$themeKey' telah dipasang ke projek CI4 anda.", 'green');
        CLI::newLine();
        CLI::write("📁 Layouts: " . realpath($targetViews), 'blue');
        CLI::write("📁 Assets:  " . realpath($targetAssets), 'blue');
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
}