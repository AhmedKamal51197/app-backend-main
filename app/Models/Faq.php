<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    protected $guarded = ['id', 'uuid'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * Return the question that's with the user locale
     */
    public function question(): string
    {
        $lang = app()->getLocale();

        if (isset($this->attributes['question_' . $lang])) {
            return $this->attributes['question_' . $lang];
        }
        return "";
    }

    /**
     * Return the answer that's with the user locale
     */
    public function answer(): string
    {
        $lang = app()->getLocale();

        if (isset($this->attributes['answer_' . $lang])) {
            return $this->attributes['answer_' . $lang];
        }
        return "";
    }
}
