<?php

namespace App\Rules;

use Closure;
use App\Models\Slot;
use Illuminate\Contracts\Validation\ValidationRule;

class SlotAvailable implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }

    public function passes($attribute, $value)
    {
        return Slot::where('id', $value)
            ->where('available_capacity', '>', 0)
            ->exists();
    }

    public function message()
    {
        return 'Le créneau sélectionné n\'est plus disponible.';
    }
}
