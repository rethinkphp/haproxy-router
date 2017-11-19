<?php

namespace rethink\hrouter\services;

use blink\core\Object;

/**
 * Class Assets
 *
 * @package rethink\hrouter\services
 */
class Assets extends Object
{
    const BASE_NAME = 'haproxy-router-ui';

    /**
     * The custom assets path, primarily used for testing purpose.
     *
     * @var string
     */
    public $customPath;

    /**
     * Returns the assets path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->customPath ?? app()->runtime . '/' . $this->getFilename(false);
    }

    protected function getFilename($withExt = false)
    {
        $name = self::BASE_NAME . '_' . ROUTER_VERSION;

        if ($withExt) {
            $name .= '.tar.gz';
        }

        return $name;
    }

    protected function getDownloadUrl()
    {
        return 'https://github.com/rethinkphp/haproxy-router-ui/releases/download/'
            . ROUTER_VERSION
            . '/'
            . $this->getFilename(true);
    }

    protected function download($url, $to)
    {
        try {
            $contents = file_get_contents($url);

            file_put_contents($to, $contents);
            return true;
        } catch (\Exception $e) {
            app()->errorHandler->handleException($e);
            return false;
        }
    }


    public function downloadAssetsIfNeeded()
    {
        if ($this->customPath) {
            return;
        }

        $path = get_existed_path($this->getPath());

        if (file_exists($path . '/index.html')) {
            return;
        }

        $url = $this->getDownloadUrl();
        $distName = $this->getFilename(true);

        $dist = app()->runtime . '/' . $distName;

        if (!$this->download($url, $dist)) {
            return;
        }

        $command = sprintf(
            'cd %s/..; tar xf %s 2>&1',
            $path,
            $distName
        );

        exec($command, $output, $retval);

        if ($retval == 0) {
            logger()->info('upgraded assets to ' . $distName);
        } else {
            logger()->error('failed to upgrade asserts, error: ' . implode(' ', $output));
        }
    }
}
