<?php

namespace App\Traits;

trait FileUpload {
    public function upload_file($file, $path) {
        $ext = $file->getClientOriginalExtension();
        $name = time().'.'.$ext;
        return $file->storeAs($path, $name);
    }
}