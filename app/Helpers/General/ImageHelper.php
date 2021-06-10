<?php


namespace App\Helpers\General;

use Illuminate\Support\Facades\Storage;


class ImageHelper
{
    protected $id;
    protected $path;
    protected $file;
    protected $name;
    protected $driver;
    protected $fileName;


    /**
     * Image constructor.
     * @param array $credentials
     */
    public function __construct($credentials = array())
    {
        $this->driver   = $credentials['driver'];
        $this->id   = $credentials['id'];
        $this->path = $credentials['s3_folder_path'];
        $this->name = $credentials['s3_storage_path'];
        $this->file = $credentials['file'];
        $this->fileName = isset($credentials['file_name']) ?  $credentials['file_name'] : null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getFile(): array
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        if (!is_null($this->fileName)) {
            return $this->fileName;
        }
        return !is_null($this->file) ? str_replace(' ', '', $this->file->getClientOriginalName()) : '';
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path . '/' . $this->id . '/' . $this->getFileName();
    }

    public function getAllFiles()
    {
        $files = [];

        foreach(Storage::disk($this->driver)->files($this->path . '/' . $this->id) as $file) {
            array_push($files, Storage::disk($this->driver)->url($file));
        };

        return $files;
    }

    /**
     * @return mixed
     */
    public function imageUrl()
    {
        return Storage::disk($this->driver)->url($this->getPath());
    }


    /**
     * @return bool
     */
    public function upload()
    {
        return Storage::disk($this->driver)->put($this->getPath(), file_get_contents($this->file));
        // return Storage::disk($this->name)->put($this->getPath(), $this->file);
    }

    /**
     * @return bool
     */
    public function delete($fileName = '')
    {
        return Storage::disk($this->driver)->delete($this->getPath());
    }

    public function deleteDirectory()
    {
        return Storage::disk($this->driver)->deleteDirectory($this->path . '/' . $this->id);
    }

    public function deleteImage($fileName)
    {
        return Storage::disk($this->driver)->delete($this->path . '/' . $this->id . '/'. $fileName);
    }

    public function to_png()
    {
        // define('UPLOAD_DIR', 'upload/'); //to save image files
        // $img = $_POST['imgURI'];
        $img = str_replace('data:image/png;base64,', '', $this->file); //replace the name of image
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file =  $this->id . '-' . uniqid() . '.png'; //with unique name each image saves

        // return $file;
        return file_put_contents($file, $data); // image put to folder upload
        // echo $success ? $file : 'Unable to save the file.';
    }
}
