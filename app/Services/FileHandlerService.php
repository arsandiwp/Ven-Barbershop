<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class FileHandlerService
{
    public function generateNameFile()
    {
        return md5(microtime());
    }


    // $from_base64 = false, $file_type = null, $original_name = null, $ext = null
    public function saveFileToStorage($file, $path, $use_original_name = false)
    {
        $storage_path = "/storage/app/public" . $path;
        /*
            example of '$path' value (the path starts with '/' and do not end the path with '/') :
            '/product/<unique_identifier>'
        */
        if ($use_original_name) {
            $file_name = $file->getClientOriginalName();
        } else {
            $file_name = $this->generateNameFile() . '.' . $file->getClientOriginalExtension();
        }

        $file->move(base_path() . $storage_path, $file_name);
        return URL::to("/storage" . $path . "/" . $file_name);
    }

    public function moveFileToAnotherDir($old_path, $new_path)
    {
        Storage::move($old_path, $new_path);
    }

    public function deleteFileFromStorage($file_path)
    {
        /*
            example of '$file_path' value (the path starts with 'public' & do not start the path with '/') :
                'public/product/<unique_identifier>/<file_name>'
        */
        if (Storage::exists($file_path)) {
            Storage::delete($file_path);
            return 1;
        } else {
            return 0;
        }
    }

    public function deleteFileItemDirectory($directory_path)
    {
        /*
            example of '$directory_path' value (the path starts with 'public', then do not start the path with '/' and do not end the path with '/') :
                'public/product/<unique_identifier>'
        */
        if (Storage::exists($directory_path)) {
            Storage::deleteDirectory($directory_path);
            return 1;
        } else {
            return 0;
        }
    }
}
