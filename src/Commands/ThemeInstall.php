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
        CLI::write('************************************', 'light_green');
        CLI::write('         IM1 Theme Installer        ', 'yellow');
        CLI::write('************************************', 'light_green');

        $versions = ['Bootstrap 5', 'Bootstrap 4', 'Bootstrap 3'];
        $bootstrap = CLI::prompt('Pilih versi Bootstrap', $versions);

        if ($bootstrap === 'Bootstrap 5') {
            $themes = ['AdminLTE', 'NiceAdmin'];
            $selected = CLI::prompt('Pilih tema', $themes);

            $themeKey = strtolower($selected);
            $vendorPath = ROOTPATH . 'vendor/mizanchan22/ci4-im-theme/stubs/';
            $sourceViews = $vendorPath . "views/layout_files/" . $themeKey;
            $sourceAssets = $vendorPath . "public/assets/". $themeKey ."/css";

            $targetViews = APPPATH . 'Views/layouts/';
            $targetAssets = FCPATH . 'assets/css/';

            helper('filesystem');
            delete_files($targetViews, true);
            delete_files($targetAssets, true);
            $this->copy_files($sourceViews, $targetViews);
            $this->copy_files($sourceAssets, $targetAssets);

            CLI::write("✅ Tema '$selected' telah dipasang ke projek CI4 anda.", 'green');
            CLI::write("📁 Layouts: file://" . str_replace('\\\\', '/', realpath($targetViews)));
            CLI::write("📁 Assets:  file://" . str_replace('\\\\', '/', realpath($targetAssets)));
             CLI::write("\n");
        }
    }

    private function copy_files(string $src, string $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);

        while (false !== ($file = readdir($dir))) {
            if ($file !== '.' && $file !== '..') {
                $srcFile = "$src/$file";
                $dstFile = "$dst/$file";

                if (is_dir($srcFile)) {
                    $this->copy_files($srcFile, $dstFile);
                } else {
                    copy($srcFile, $dstFile);
                }
            }
        }

        closedir($dir);
    }
}