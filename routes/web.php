<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Http\Controllers\ProjectContractController;

// Project contract preview
// Project contract preview
Route::get('/admin/projects/{project}/contract', [ProjectContractController::class, 'show'])
    ->name('projects.contract')
    ->middleware('auth');

// Project report preview
Route::get('/admin/projects/{project}/report', [\App\Http\Controllers\ProjectReportController::class, 'show'])
    ->name('projects.report')
    ->middleware('auth');

// Homepage
Route::get('/', [PageController::class, 'home'])->name('home');

// About page
Route::get('/about', [PageController::class, 'about'])->name('about');

// Timeline page
Route::get('/timeline', [PageController::class, 'timeline'])->name('timeline');

// Services page
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/services/{slug}', [PageController::class, 'serviceDetail'])->name('services.show');

// Portfolio page
Route::get('/portfolio', [PageController::class, 'portfolio'])->name('portfolio');

// Blog page
Route::get('/blog', [PageController::class, 'blog'])->name('blog');

// Contact page
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Contact form submission
Route::post('/contact', function (Request $request) {
    $validated = $request->validate([
        'full_name' => 'required|string|max:100',
        'email' => 'required|email|max:100',
        'phone' => 'nullable|string|max:20',
        'subject' => 'nullable|string|max:150',
        'message' => 'required|string',
    ]);

    Inquiry::create($validated);

    return back()->with('success', 'Thank you for your message! We will get back to you soon.');
})->name('inquiries.store');

// Sitemap routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-pages.xml', [\App\Http\Controllers\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-blog.xml', [\App\Http\Controllers\SitemapController::class, 'blog'])->name('sitemap.blog');
Route::get('/sitemap-services.xml', [\App\Http\Controllers\SitemapController::class, 'services'])->name('sitemap.services');

// Robots.txt
Route::get('/robots.txt', function () {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Disallow: /gp-strategix\n";
    $content .= "Disallow: /gp-strategix/*\n\n";
    $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
    
    return response($content, 200)->header('Content-Type', 'text/plain');
})->name('robots');

// Newsletter subscription
Route::post('/newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Redirect /login to Filament admin login
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

// Blog article detail (catch-all, must be LAST route)
Route::get('/{slug}', [PageController::class, 'articleDetail'])->name('blog.show');

