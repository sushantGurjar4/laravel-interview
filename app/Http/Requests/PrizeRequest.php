<?php

namespace App\Http\Requests;

use App\Models\Prize;
use Illuminate\Foundation\Http\FormRequest;

class PrizeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {




        return
            [
                'title' => 'required',
                'probability' => 'required|numeric|min:0|max:100',
            ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $prizeId = $this->route('prize');
            $currentPrize = Prize::find($prizeId);
            $totalProbability = Prize::where('id', '!=', $prizeId)->sum('probability') + $this->input('probability', 0);

            if ($totalProbability > 100) {
                $validator->errors()->add('probability', 'The total probability of all prizes must not exceed 100%.');
            }
        });
    }
}
