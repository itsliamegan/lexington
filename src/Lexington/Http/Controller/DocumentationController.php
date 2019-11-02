<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Model\DocumentationArticle;
use Lexington\Model\User;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class DocumentationController
{
    public static function index()
    {
        $articles = DocumentationArticle::all();
        $user     = User::findOrFail(Session::get('user_id'));

        Response::view('documentation/index', [
            'title'        => 'Documentation',
            'current_user' => $user,
            'articles'     => $articles
        ]);
    }

    public static function new()
    {
        $user = User::findOrFail(Session::get('user_id'));

        if (! $user->is_admin) {
            Response::redirect('/lexington/documentation');
        }

        Response::view('documentation/edit', [
            'title'        => 'New Documentation',
            'current_user' => $user
        ]);
    }

    public static function create()
    {
        $body    = Request::body();
        $title   = $body['title'];
        $slug    = $body['slug'];
        $content = $body['content'];
        $user    = User::findOrFail(Session::get('user_id'));

        if (! $user->is_admin) {
            Response::redirect('/lexington/documentation');
        }

        $article = DocumentationArticle::create([
            'title'      => $title,
            'slug'       => $slug,
            'content'    => $content,
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        Response::redirect("/lexington/documentation/{$article->slug}");
    }

    public static function show($slug)
    {
        $article = DocumentationArticle::show($slug);
        $user    = User::findOrFail(Session::get('user_id'));

        $article_markdown_content = (new \Parsedown)
            ->setMarkupEscaped(true)
            ->text($article->content);

        Response::view('documentation/show', [
            'title'                    => $article->title,
            'current_user'             => $user,
            'article'                  => $article,
            'article_markdown_content' => $article_markdown_content
        ]);
    }

    public static function edit($slug)
    {
        $article = DocumentationArticle::show($slug);
        $user    = User::findOrFail(Session::get('user_id'));

        if (! $user->is_admin) {
            Response::redirect('/lexington/documentation');
        }

        Response::view('documentation/edit', [
            'title'        => $article->title,
            'current_user' => $user,
            'article'      => $article
        ]);
    }

    public static function update($slug)
    {
        $body     = Request::body();
        $title    = $body['title'];
        $new_slug = $body['slug'];
        $content  = $body['content'];
        $article  = DocumentationArticle::show($slug);
        $user     = User::findOrFail(Session::get('user_id'));


        if (! $user->is_admin) {
            Response::redirect('/lexington/documentation');
        }

        $article->fill([
            'title'      => $title,
            'slug'       => $new_slug,
            'content'    => $content,
            'updated_by' => $user->id
        ]);
        $article->save();
        $article->refresh();

        Response::redirect("/lexington/documentation/{$article->slug}");
    }

    public static function delete($slug)
    {
        $article = DocumentationArticle::show($slug);
        $user    = User::findOrFail(Session::get('user_id'));

        if (! $user->is_admin) {
            Response::redirect('/lexington/documentation');
        }

        $article->delete();

        Response::redirect('/lexington/documentation');
    }
}
