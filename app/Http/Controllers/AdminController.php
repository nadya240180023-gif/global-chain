<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Port;
use App\Models\Country;
use App\Models\PositiveWord;
use App\Models\NegativeWord;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function usersIndex()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function wordsIndex()
    {
        $positiveWords = PositiveWord::orderBy('word')->get();
        $negativeWords = NegativeWord::orderBy('word')->get();
        return view('admin.words', compact('positiveWords', 'negativeWords'));
    }

    public function portsIndex()
    {
        $ports = Port::with('country')->orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        return view('admin.ports', compact('ports', 'countries'));
    }

    public function articlesIndex()
    {
        $articles = Article::with('author')->orderBy('created_at', 'desc')->get();
        return view('admin.articles', compact('articles'));
    }

    public function toggleUserStatus(User $user)
    {
        // Prevent deleting oneself
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        // Don't allow changing primary admin email to prevent lockouts
        $isAdmin = $user->email === 'admin@gsc.com';

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ];

        $request->validate($rules);

        $user->name = $request->name;
        if (!$isAdmin) {
            $user->email = $request->email;
        }
        
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Informasi pengguna berhasil diperbarui.');
    }

    public function storePort(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:ports,code',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Port::create($request->all());

        return redirect()->back()->with('success', 'Pelabuhan baru berhasil ditambahkan ke dataset.');
    }

    public function destroyPort(Port $port)
    {
        $port->delete();
        return redirect()->back()->with('success', 'Pelabuhan berhasil dihapus dari dataset.');
    }

    public function storeWord(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:50|alpha',
            'type' => 'required|in:positive,negative',
        ]);

        $word = strtolower($request->word);

        if ($request->type === 'positive') {
            PositiveWord::updateOrCreate(['word' => $word]);
        } else {
            NegativeWord::updateOrCreate(['word' => $word]);
        }

        return redirect()->back()->with('success', "Kata '{$word}' berhasil ditambahkan ke kamus.");
    }

    public function destroyWord(string $type, string $word)
    {
        if ($type === 'positive') {
            PositiveWord::where('word', $word)->delete();
        } else {
            NegativeWord::where('word', $word)->delete();
        }

        return redirect()->back()->with('success', "Kata '{$word}' berhasil dihapus dari kamus.");
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Article::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . rand(100, 999),
            'content' => $request->content,
            'author_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Artikel analisis berhasil dipublikasikan.');
    }

    public function destroyArticle(Article $article)
    {
        $article->delete();
        return redirect()->back()->with('success', 'Artikel analisis berhasil dihapus.');
    }
}
