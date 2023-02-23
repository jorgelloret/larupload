<?php

namespace Mostafaznv\Larupload\Concerns\Storage\UploadEntity;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Mostafaznv\Larupload\Helpers\Slug;
use Mostafaznv\Larupload\LaruploadEnum;
use Mostafaznv\Larupload\UploadEntities;

trait UploadEntityName
{
    /**
     * Name of file
     */
    protected string $name;

    /**
     * Name of file in kebab case
     */
    protected string $nameKebab;

    /**
     * Specify the method that Larupload should use to name uploaded files.
     */
    protected string $namingMethod;

    /**
     * Language of file name
     */
    protected ?string $lang;


    public function getName(bool $withNameStyle = false): string
    {
        return $withNameStyle ? $this->nameStyle($this->name) : $this->name;
    }

    public function namingMethod(string $method): UploadEntities
    {
        $this->validateNamingMethod($method);

        $this->namingMethod = $method;

        return $this;
    }

    public function lang(string $lang): UploadEntities
    {
        $this->lang = $lang;

        return $this;
    }

    protected function setFileName(UploadedFile $file = null): string
    {
        $file = $file ?? $this->file;
        $format = $file->getClientOriginalExtension();

        switch ($this->namingMethod) {
            case LaruploadEnum::HASH_FILE_NAMING_METHOD:
                $name = hash_file('md5', $file->getRealPath());
                break;

            case LaruploadEnum::TIME_NAMING_METHOD:
                $name = time();
                break;

            default:
                $name = $file->getClientOriginalName();
                $name = pathinfo($name, PATHINFO_FILENAME);
                $num = rand(0, 9999);

                $slug = Slug::make($this->lang)->generate($name);
                $name = "$slug-$num";
                break;
        }

        return "$name.$format";
    }

    /**
     * Check whether we should convert the name to camel-case style.
     */
    protected function nameStyle($name): string
    {
        return $this->camelCaseResponse ? Str::camel($name) : $name;
    }

    /**
     * In some special cases we should use other file names instead of the original one.
     *
     * Example: when user uploads a svg image, we should change the converted format to jpg!
     * so we have to manipulate file name
     */
    protected function fixExceptionNames(string $name, string $style): string
    {
        if (!in_array($style, [LaruploadEnum::ORIGINAL_FOLDER, LaruploadEnum::COVER_FOLDER])) {
            if (Str::endsWith($name, 'svg')) {
                $name = str_replace('svg', 'jpg', $name);
            }
        }

        return $name;
    }


    private function validateNamingMethod(string $method): void
    {
        $allowedMethods = [
            LaruploadEnum::SLUG_NAMING_METHOD,
            LaruploadEnum::HASH_FILE_NAMING_METHOD,
            LaruploadEnum::TIME_NAMING_METHOD
        ];

        if (!in_array($method, $allowedMethods)) {
            $allowedMethods = implode(', ', $allowedMethods);

            throw new Exception("Naming method [$method] is not valid. valid methods: [$allowedMethods]");
        }
    }
}