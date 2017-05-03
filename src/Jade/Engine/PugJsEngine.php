<?php

namespace Jade\Engine;

use NodejsPhpFallback\NodejsPhpFallback;

/**
 * Class Jade\PugJsEngine.
 */
class PugJsEngine extends Options
{
    protected function getPugJsOptions(&$input, &$filename, &$vars, &$pug)
    {
        if (is_array($filename)) {
            $vars = $filename;
            $filename = null;
        }

        $workDirectory = empty($this->options['cache'])
            ? sys_get_temp_dir()
            : $this->options['cache'];
        $pug = true;
        if ($filename === null && file_exists($input)) {
            $filename = $input;
            $pug = null;
        }
        if ($pug) {
            $pug = $input;
            $input = $workDirectory . '/source.pug';
            file_put_contents($input, $pug);
        }

        $options = array(
            'path' => realpath($filename),
            'basedir' => $this->options['basedir'],
            'pretty' => $this->options['prettyprint'],
            'out' => $workDirectory,
        );
        if (!empty($vars)) {
            $options['obj'] = json_encode($vars);
        }

        return $options;
    }

    protected function parsPugJsResult($result, &$input, &$pug)
    {
        $result = explode('rendered ', $result);
        if (count($result) < 2) {
            throw new \RuntimeException(
                'Pugjs throw an error: ' . $result[0]
            );
        }
        $file = trim($result[1]);
        $html = file_get_contents($file);
        unlink($file);

        if ($pug) {
            unlink($input);
        }

        return $html;
    }

    /**
     * Render using the native Pug JS engine.
     *
     * @param string   $input    pug input or file
     * @param string   $filename optional file path
     * @param array    $vars     to pass to the view
     * @param callable $fallback called if JS engine not available
     *
     * @throws \Exception
     *
     * @return string
     */
    public function renderWithJs($input, $filename, array $vars, $fallback)
    {
        $options = $this->getPugJsOptions($input, $filename, $vars, $pug);
        $args = array();

        foreach ($options as $option => $value) {
            if (!empty($value)) {
                $args[] = '--' . $option . ' ' . json_encode($value);
            }
        }

        $node = new NodejsPhpFallback();
        $result = $node->execModuleScript(
            'pug-cli',
            'index.js',
            implode(' ', $args) .
            ' ' . escapeshellarg($input) .
            ' 2>&1',
            $fallback
        );

        return $this->parsPugJsResult($result, $input, $pug);
    }
}
