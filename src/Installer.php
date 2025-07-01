<?php

namespace IM1\Installer;

class Installer
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = getcwd(); // CI4 root
    }

    public function run(): void
    {
        echo "\n";
        echo str_repeat("*", 100) . "\n";
        echo str_pad("IM1 R&D Team", 100, " ", STR_PAD_BOTH) . "\n";
        echo str_repeat("*", 100) . "\n\n";

        echo "1. Bootstrap 5\n";
        echo "2. Bootstrap 4\n";
        echo "3. Bootstrap 3\n";
        echo "\nPilih versi Bootstrap (1-3): ";
        $version = trim(fgets(STDIN));

        if ($version === '1') {
            $this->selectBootstrap5();
        } else {
            echo "Versi belum disokong lagi.\n";
        }
    }

    protected function selectBootstrap5(): void
    {
        echo "\nTema Bootstrap 5:\n";
        echo "1. AdminLTE\n";
        echo "2. NiceAdmin\n";
        echo "\nPilih tema (1-2): ";
        $theme = trim(fgets(STDIN));

        $themeMap = [
            '1' => 'adminlte',
            '2' => 'niceadmin',
        ];

        if (!isset($themeMap[$theme])) {
            echo "Pilihan tidak sah.\n";
            return;
        }

        $this->copyTheme($themeMap[$theme]);
    }

    protected function copyTheme(string $theme): void
    {
        $sourceView = __DIR__ . "/../stubs/views/layout_files/{$theme}";
        $sourceAsset = __DIR__ . "/../stubs/public/assets/{$theme}";

        $targetView = "{$this->basePath}/app/Views/layouts";
        $targetAsset = "{$this->basePath}/public/assets";

        $this->recreateFolder($targetView);
        $this->recreateFolder($targetAsset);

        $this->copyDirectory($sourceView, $targetView);
        $this->copyDirectory($sourceAsset, $targetAsset);

        echo "\n✅ Tema '$theme' berjaya dipasang.\n";
        echo "\n📁 Layouts copied to:   $targetView";
        echo "\n📁 Assets copied to:    $targetAsset\n";
        echo "\n Bye";
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