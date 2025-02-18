<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class DesktopAPI {
    private $base_path;

    public function __construct($base_path = null) {
        if ($base_path) {
            $this->base_path = dirname(__DIR__ . "/");
        } else {
            // Default to a specific directory on the web server
            $this->base_path = dirname(__DIR__ . "/");
        }
    }

    public function getContents($path = '') {
        $fullPath = $this->base_path . ($path ? DIRECTORY_SEPARATOR . $path : '');
        
        if (!file_exists($fullPath)) {
            return false;
        }

        $items = scandir($fullPath);
        $result = array(
            'files' => array(),
            'folders' => array()
        );

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $itemPath = $fullPath . DIRECTORY_SEPARATOR . $item;
            $itemInfo = array(
                'name' => $item,
                'path' => str_replace($this->base_path, '', $itemPath),
                'size' => filesize($itemPath),
                'modified' => filemtime($itemPath),
                'created' => filectime($itemPath)
            );

            if (is_dir($itemPath)) {
                $result['folders'][] = $itemInfo;
            } else {
                $itemInfo['extension'] = pathinfo($item, PATHINFO_EXTENSION);
                $result['files'][] = $itemInfo;
            }
        }

        return $result;
    }

    public function createFolder($path) {
        $fullPath = $this->base_path . DIRECTORY_SEPARATOR . $path;
        
        if (file_exists($fullPath)) {
            return false;
        }

        return mkdir($fullPath, 0777, true);
    }
    public function createFolderInParent($parentPath, $newFolderName) {
        $fullParentPath = realpath($this->base_path) . DIRECTORY_SEPARATOR . trim($parentPath, DIRECTORY_SEPARATOR);
        $fullParentPath = realpath($this->base_path) . DIRECTORY_SEPARATOR . trim($parentPath, " \\/");

        if (!file_exists($fullParentPath) || !is_dir($fullParentPath)) {
            return false;
        }

        $newFolderPath = $fullParentPath . DIRECTORY_SEPARATOR . $newFolderName;

        if (file_exists($newFolderPath)) {
            return false;
        }

        mkdir($newFolderPath, 0777, true);
        return true;
    }
    public function delete($path) {
        $fullPath = $this->base_path . DIRECTORY_SEPARATOR . $path;
        
        if (!file_exists($fullPath)) {
            return false;
        }

        if (is_dir($fullPath)) {
            return $this->deleteDirectory($fullPath);
        } else {
            return unlink($fullPath);
        }
    }

    public function search($keyword) {
        $results = array();
        $this->searchInDirectory($this->base_path, $keyword, $results);

        return $results;
    }

    private function searchInDirectory($dir, $keyword, &$results) {
        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            
            if (stripos($item, $keyword) !== false) {
                $results[] = array(
                    'name' => $item,
                    'path' => str_replace($this->base_path, '', $path),
                    'type' => is_dir($path) ? 'folder' : 'file',
                    'size' => filesize($path)
                );
            }

            if (is_dir($path)) {
                $this->searchInDirectory($path, $keyword, $results);
            }
        }
    }

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) return false;

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }

    public function searchFolder($folderName, $searchPath = '') {
        $results = array();
        $fullPath = $this->base_path . ($searchPath ? DIRECTORY_SEPARATOR . $searchPath : '');
        
        if (!file_exists($fullPath) || !is_dir($fullPath)) {
            return false;
        }
        return true;
    }
}
?>
