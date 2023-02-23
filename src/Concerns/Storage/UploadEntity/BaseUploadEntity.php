<?php

namespace Mostafaznv\Larupload\Concerns\Storage\UploadEntity;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Mostafaznv\Larupload\LaruploadEnum;
use Mostafaznv\Larupload\UploadEntities;

trait BaseUploadEntity
{
    public function __construct(string $name, string $mode)
    {
        $config = config('larupload');

        $this->name = $name;
        $this->nameKebab = str_replace('_', '-', Str::kebab($name));
        $this->mode = $mode;
        $this->disk = $config['disk'];
        $this->localDisk = $config['local-disk'];
        $this->withMeta = $config['with-meta'];
        $this->camelCaseResponse = $config['camel-case-response'];
        $this->namingMethod = $config['naming-method'];
        $this->lang = $config['lang'];
        $this->imageProcessingLibrary = $config['image-processing-library'];
        $this->generateCover = $config['generate-cover'];
        $this->coverStyle = $config['cover-style'];
        $this->dominantColor = $config['dominant-color'];
        $this->keepOldFiles = $config['keep-old-files'];
        $this->preserveFiles = $config['preserve-files'];
        $this->ffmpegQueue = $config['ffmpeg']['queue'];
        $this->ffmpegMaxQueueNum = $config['ffmpeg']['max-queue-num'];
        $this->ffmpegCaptureFrame = $config['ffmpeg']['capture-frame'];
    }

    public static function make(string $name, string $mode = LaruploadEnum::HEAVY_MODE): UploadEntities
    {
        return new static($name, $mode);
    }


    public function getMode(): string
    {
        return $this->mode;
    }

    public function setOutput(Model $model): void
    {
        $this->id = $model->id;

        if ($this->mode == LaruploadEnum::HEAVY_MODE) {
            foreach ($this->output as $key => $value) {
                $this->output[$key] = $model->{"{$this->name}_file_$key"};
            }
        }
        else {
            $meta = json_decode($model->{"{$this->name}_file_meta"}, true);

            if (is_array($meta)) {
                foreach ($meta as $key => $value) {
                    $this->output[$key] = $value;
                }
            }
        }
    }

    /**
     * Prepare output array to response
     *
     * @return object
     */
    protected function outputToObject(): object
    {
        $output = (object)$this->output;

        if ($this->camelCaseResponse) {
            $output->mimeType = $output->mime_type;
            $output->dominantColor = $output->dominant_color;

            unset($output->mime_type);
            unset($output->dominant_color);
        }

        return $output;
    }
}