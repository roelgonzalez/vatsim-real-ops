<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeEvent extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date',
            'end_time' => 'required|date_format:H:i',
            'banner_url' => 'nullable|url',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The event requires a title',
            'description.required' => 'The event requires a description',
            'start_date.required' => 'The event requires a start date',
            'start_date.date' => 'The event requires a valid start date',
            'start_time.required' => 'The event requires a start time',
            'start_time.date' => 'The event requires a valid start time',
            'end_date.required' => 'The event requires an end date',
            'end_date.date' => 'The event requires a valid end date',
            'end_time.required' => 'The event requires an end time',
            'end_time.date' => 'The event requires a valid end time',
            'banner_url.url' => 'Please enter a valid url for the event banner',
        ];
    }
}
