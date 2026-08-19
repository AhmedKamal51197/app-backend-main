<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * A class defines the notification setting model
 */
class NotificationSetting extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Return the setting_name fit with the user locale
     *
     * @return string
     */
    public function setting_name():string
    {
        $lang =  app()->getLocale();

        if (isset($this->attributes['setting_name_' . $lang])) {
            return $this->attributes['setting_name_' . $lang] ;
        }
        return "";
    }

    /**
     * Return the description fit with the user locale
     *
     * @return string
     */
    public function description():string
    {
        $lang =  app()->getLocale();

        if (isset($this->attributes['description_' . $lang])) {
            return $this->attributes['description_' . $lang] ;
        }
        return "";
    }

}
