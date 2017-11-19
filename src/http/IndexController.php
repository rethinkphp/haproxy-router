<?php

namespace rethink\hrouter\http;

use blink\core\Object;

/**
 * Class IndexController
 *
 * @package rethink\hrouter\restapi
 */
class IndexController extends Object
{
    public function renderChallenge($token)
    {
        $challenge = challenges()->loadByToken($token);

        if ($challenge) {
            return $challenge->payload;
        }
    }

    protected function resolveRequestedPath($args)
    {
        $basePath = app()->assets->getPath();

        if (empty($args)) {
            $args = ['index.html'];
        }

        $path = $basePath . '/' . implode('/', $args);

        if (!file_exists($path)) {
            $path = $basePath . '/index.html';
        }

        return $path;
    }

    public function renderAssets()
    {
        $path = $this->resolveRequestedPath(func_get_args());

        $request = request();
        $response = response();

        $response->with(file_get_contents($path));

        $lastModified = filemtime($path);

        $since =  $request->headers->first('If-Modified-Since');

        if ($since) {
            /** @var \DateTime $dt */
            $dt = \DateTime::createFromFormat('D, d M Y H:i:s T', $since);
            if ($lastModified <= $dt->getTimestamp()) {
                $response->status(304);
                return;
            }
        }

        $response->headers->with('Content-Type', $this->getMimeType($path));
        $response->headers->with('Last-Modified', gmdate('D, d M Y H:i:s T', $lastModified));
        $response->headers->with('Cache-Control', 'public, max-age=1800');

        return $response;
    }

    protected function getMimeType($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $map = [
            'html' => 'text/html',
            'js' => 'application/javascript',
        ];

        return $map[$ext] ?? 'text/plain';
    }
}
