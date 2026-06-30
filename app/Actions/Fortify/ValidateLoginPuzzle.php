<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ValidateLoginPuzzle
{
    /**
     * Validate the login puzzle nonce and decode the obfuscated password.
     *
     * This action ensures:
     * 1. The nonce is valid and single-use (replay attack prevention).
     * 2. The password is decoded from the puzzle obfuscation back to plaintext.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle(Request $request, callable $next): mixed
    {
        \Illuminate\Support\Facades\Log::info('ENTERED PUZZLE ACTION', $request->all());
        
        $nonce = $request->input('login_nonce');

        if (!$nonce) {
            throw ValidationException::withMessages([
                'email' => 'Sesi login tidak valid. Silakan muat ulang halaman.',
            ]);
        }

        $nonceKey = 'login_nonce_' . $request->session()->getId() . '_' . $nonce;
        $puzzle = Cache::pull($nonceKey);

        if (!$puzzle) {
            throw ValidationException::withMessages([
                'email' => 'Sesi login tidak valid atau sudah digunakan. Silakan muat ulang halaman.',
            ]);
        }

        $hex = $request->input('password', '');
        $decoded = $this->decodePuzzle($hex, $puzzle);
        
        \Illuminate\Support\Facades\Log::info('Puzzle Validation', [
            'hex' => $hex,
            'decoded' => $decoded,
            'action' => $puzzle['action'] ?? null,
            'key' => $puzzle['key'] ?? null,
        ]);
        
        $request->merge(['password' => $decoded]);

        return $next($request);
    }

    /**
     * Reverse the puzzle obfuscation applied by the frontend.
     *
     * The frontend encodes each character as a 6-digit hex code after applying
     * the puzzle action (reverse, shift, or xor). This method undoes that transformation.
     */
    private function decodePuzzle(string $hex, array $puzzle): string
    {
        if (strlen($hex) === 0 || strlen($hex) % 6 !== 0) {
            return $hex;
        }

        $chars = [];

        foreach (str_split($hex, 6) as $chunk) {
            $code = hexdec($chunk);

            if ($puzzle['action'] === 'shift') {
                $code -= $puzzle['key'];
            } elseif ($puzzle['action'] === 'xor') {
                $code ^= $puzzle['key'];
            }

            $chars[] = mb_chr($code, 'UTF-8');
        }

        if ($puzzle['action'] === 'reverse') {
            $chars = array_reverse($chars);
        }

        return implode('', $chars);
    }
}
