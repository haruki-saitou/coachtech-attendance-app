<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class UpdateAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'check_in_at' => ['required', 'date_format:H:i'],
            'check_out_at' => ['required', 'after:check_in_at', 'date_format:H:i'],
            'rest_start.*' => ['nullable', 'date_format:H:i', 'after_or_equal:check_in_at', 'before_or_equal:check_out_at'],
            'rest_end.*' => ['nullable', 'after:rest_start.*', 'date_format:H:i', 'before_or_equal:check_out_at'],
            'comment' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'check_in_at.required' => '出勤時間を記入してください',
            'check_in_at.date_format' => '出勤時間は時刻形式で入力してください',
            'check_out_at.required' => '退勤時間を記入してください',
            'check_out_at.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'check_out_at.date_format' => '退勤時間は時刻形式で入力してください',
            'rest_start.*.date_format' => '休憩時間は時刻形式で入力してください',
            'rest_start.*.after_or_equal' => '休憩時間が不適切な値です',
            'rest_start.*.before_or_equal' => '休憩時間が不適切な値です',
            'rest_end.*.date_format' => '休憩時間は時刻形式で入力してください',
            'rest_end.*.after' => '休憩時間が不適切な値です',
            'rest_end.*.before_or_equal' => '休憩時間もしくは退勤時間が不適切な値です',
            'comment.required' => '備考を記入してください',
            'comment.max' => '備考は255文字以内で入力してください',
        ];
    }
}