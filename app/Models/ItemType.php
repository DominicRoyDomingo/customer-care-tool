<?php

namespace App\Models;

use App\Models\MedicalStuff\MedicalArticle;
use App\Models\Traits\BaseName;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use BaseName;

    protected $table = 'publishing_item_type';

    protected $guarded = ['id'];

    protected $fillable = ['type_name'];

    protected $courses = ['course', 'courses', 'corso', 'corsi'];


    protected $appends = [
        'item_type_name',
        'is_loading',
        'is_english',
        'is_italian',
        'is_german',
        'is_bisaya',
        'is_filipino',
        'has_translation',
        'is_rendered',
        'base_name',
        'base_language',
    ];

    public function getBaseNameAttribute()
    {
        return $this->get_base_name();
    }

    public function getItemTypeNameAttribute()
    {
        return $this->get_base_name(true);
    }

    public function getIsRenderedAttribute()
    {
        return true;
    }

    public function getHasTranslationAttribute()
    {
        $baseName = $this->base_name();

        if (!$baseName) {
            return false;
        }

        return true;
    }

    /**
     * @return String
     */
    public function getIsEnglishAttribute()
    {
        return string_to_value('en', $this->type_name);
    }

    /**
     * @return String
     */
    public function getIsGermanAttribute()
    {
        return string_to_value('de', $this->type_name);
    }

    /**
     * @return String
     */
    public function getIsItalianAttribute()
    {
        return string_to_value('it', $this->type_name);
    }

    public function getIsBisayaAttribute()
    {
        return string_to_value('ph-bis', $this->type_name);
    }

    public function getIsFilipinoAttribute()
    {
        return string_to_value('ph-fil', $this->type_name);
    }


    public function getBaseLanguageAttribute()
    {
        if ($this->is_english) {
            return 'en';
        }

        if ($this->is_italian) {
            return 'it';
        }

        if ($this->is_german) {
            return 'de';
        }

        if ($this->is_filipino) {
            return 'ph-fil';
        }

        if ($this->is_bisaya) {
            return 'ph-bis';
        }
    }

    public function getIsLoadingAttribute()
    {
        return false;
    }


    public function create($data)
    {
        $type = new ItemType;

        $type->type_name = $data['type_name'];

        $type->save();

        return $type;
    }


    public function changeData($data)
    {

        $formData = json_decode($data->input('data'));

        $lang = $formData->language;

        $type = ItemType::whereId($formData->id)->first();

        $typeVal = string_add_json($lang, ucwords($formData->type_name), string_remove($lang, $type->type_name));

        $type->type_name = $typeVal;

        $type->save();

        return $type;
    }

    public function articles(){
        return $this->belongsToMany(MedicalArticle::class, 'publishing_item_type_link', 'publishing_item_type_id', 'article_id');
    }
}
