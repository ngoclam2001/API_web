<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class DesktopAPI {
    private $desktop_path;

    public function __construct() {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->desktop_path = $_SERVER['USERPROFILE'] . "\\Desktop";
        } else {
            $this->desktop_path = $_SERVER['HOME'] . "/Desktop";
        }
        
    }

    public function getContents($path = '') {
        try {
            $fullPath = $this->desktop_path . ($path ? DIRECTORY_SEPARATOR . $path : '');
            
            if (!file_exists($fullPath)) {
                throw new Exception("Không tìm thấy đường dẫn!!!");
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
                    'path' => str_replace($this->desktop_path, '', $itemPath),
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

            return $this->sendResponse($result);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function createFolder($path) {
        try {
            $fullPath = $this->desktop_path . DIRECTORY_SEPARATOR . $path;
            
            if (file_exists($fullPath)) {
                throw new Exception("Thư mục đã tồn tại !!!");
            }

            if (mkdir($fullPath, 0777, true)) {
                return $this->sendResponse(array(
                    'message' => 'Folder created successfully',
                    'path' => $path
                ));
            } else {
                throw new Exception("Failed to create folder");
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function delete($path) {
        try {
            $fullPath = $this->desktop_path . DIRECTORY_SEPARATOR . $path;
            
            if (!file_exists($fullPath)) {
                throw new Exception("Path not found");
            }

            if (is_dir($fullPath)) {
                $this->deleteDirectory($fullPath);
            } else {
                unlink($fullPath);
            }

            return $this->sendResponse(array(
                'message' => 'Item deleted successfully',
                'path' => $path
            ));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function search($keyword) {
        try {
            $results = array();
            $this->searchInDirectory($this->desktop_path, $keyword, $results);

            return $this->sendResponse($results);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    private function searchInDirectory($dir, $keyword, &$results) {
        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            
            if (stripos($item, $keyword) !== false) {
                $results[] = array(
                    'name' => $item,
                    'path' => str_replace($this->desktop_path, '', $path),
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
        if (!file_exists($dir)) return;

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

    private function sendResponse($data) {
        http_response_code(200);
        return json_encode($data);
    }

    private function sendError($message) {
        http_response_code(500);
        return json_encode(array('error' => $message));
    }
}

?>