<?php

class FormRequest
{
    static protected $formData = array();

    static protected $fillable = array(
        'name',
        'tel',
        'email',
        'detail',
        'csrfToken'
    );

    public static function validateFormRequest(array $request)
    {
        $form_items = self::$fillable;

        foreach ($form_items as $item) {
            self::$formData[$item] = filter_var($request[$item], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        return self::$formData;
    }
}