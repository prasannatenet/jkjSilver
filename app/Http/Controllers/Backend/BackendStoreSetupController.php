<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BackendStoreSetupController extends Controller
{
    public function edit()
    {
        $setting = Setting::query()->first();

        return view('backend.store-setup.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $policyLength = static function (mixed $value): int {
            $points = is_array($value) ? array_filter($value, 'is_string') : [];

            return Str::length(implode('', $points));
        };

        $data = $request->validate([
            'shipping_policy' => [
                'nullable',
                'array',
                function (string $attribute, mixed $value, \Closure $fail) use ($policyLength): void {
                    if ($policyLength($value) > 500) {
                        $fail('The shipping policy must not exceed 500 characters in total.');
                    }
                },
            ],
            'shipping_policy.*' => 'nullable|string|max:500',
            'return_policy' => [
                'nullable',
                'array',
                function (string $attribute, mixed $value, \Closure $fail) use ($policyLength): void {
                    if ($policyLength($value) > 500) {
                        $fail('The return policy must not exceed 500 characters in total.');
                    }
                },
            ],
            'return_policy.*' => 'nullable|string|max:500',
            'free_shipping_minimum' => 'required|numeric|min:0|max:99999999.99',
        ]);

        foreach (['shipping_policy', 'return_policy'] as $policy) {
            $data[$policy] = array_values(array_filter(
                $data[$policy] ?? [],
                fn ($point): bool => is_string($point) && trim($point) !== '',
            ));
        }

        $setting = Setting::query()->first() ?? new Setting();
        $setting->fill($data)->save();

        return redirect()->route('admin.store-setup.edit')
            ->with('success', 'Store setup updated successfully.');
    }
}
