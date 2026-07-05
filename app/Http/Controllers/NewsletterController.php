<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Handle newsletter subscription
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('email'),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $email = strtolower(trim($request->email));
        $source = $request->input('source', 'website');

        try {
            // Check if already subscribed
            if (NewsletterSubscriber::isSubscribed($email)) {
                $message = 'Email ini sudah terdaftar dalam newsletter kami.';
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'already_subscribed' => true,
                    ]);
                }
                return back()->with('info', $message);
            }

            // Subscribe
            NewsletterSubscriber::subscribe($email, $source);
            
            $message = 'Terima kasih! Anda berhasil berlangganan newsletter kami.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            $message = 'Terjadi kesalahan. Silakan coba lagi.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }
            
            return back()->with('error', $message);
        }
    }

    /**
     * Handle newsletter unsubscription
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak valid.',
            ], 422);
        }

        $subscriber = NewsletterSubscriber::where('email', strtolower(trim($request->email)))->first();
        
        if ($subscriber) {
            $subscriber->unsubscribe();
            return response()->json([
                'success' => true,
                'message' => 'Anda telah berhasil berhenti berlangganan.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email tidak ditemukan.',
        ], 404);
    }
}
