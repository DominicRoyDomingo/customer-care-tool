<?php

namespace App\Models\Traits;


/**
 * Trait Uuid.
 */
trait BaseName
{
    public function base_name()
    {
        $baseName = $this->is_english;
        if (locale() === 'it')  $baseName = $this->is_italian;
        if (locale() === 'de')  $baseName = $this->is_german;
        if (locale() === 'ph-fil')  $baseName = $this->is_filipino;
        if (locale() === 'ph-bis')  $baseName = $this->is_bisaya;

        return $baseName;
    }

    public function get_base_name($key = false)
    {
        $baseName = $this->base_name();

        if (!$baseName) {
            $priorityBaseName = $this->is_english ?? $this->is_italian;
            $priorityBaseName = $priorityBaseName ?? $this->is_german;
            $priorityBaseName = $priorityBaseName ?? $this->is_filipino;
            $baseName .= $priorityBaseName ?? $this->is_bisaya;

            if ($key) {
                $language = language();
                $baseName .= "<i class='text-danger ml-1' style='font-size:10px'>(No {$language} translation yet)</i>";
            }
        }

        return $baseName;
    }
}
