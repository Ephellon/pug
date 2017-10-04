<?php

namespace Pug;

use InvalidArgumentException;
use Phug\Renderer\Adapter\StreamAdapter;
use Pug\Engine\Options;

/**
 * Class Pug\Pug.
 */
class Pug extends Options
{
    public function __construct($options = null)
    {
        $this->setUpDefaultOptions($options);
        $this->copyNormalizedOptions($options);
        $this->setUpFilterAutoload($options);
        $this->setUpOptionsAliases($options);
        $this->setUpFormats($options);
        $this->setUpCache($options);
        $this->setUpMixins($options);
        $this->setUpEvents($options);
        $this->setUpJsPhpize($options);
        $this->setUpAttributesMapping($options);

        parent::__construct($options);

        $this->initializeLimits();
        $this->initializeJsPhpize();
    }

    public function setCustomOption($name, $value)
    {
        return $this->setOption($name, $value);
    }

    public function setCustomOptions($options)
    {
        return $this->setOptions($options);
    }

    /**
     * Returns list of requirements in an array identified by keys.
     * For each of them, the value can be true if the requirement is
     * fulfilled, false else.
     *
     * If a requirement name is specified, returns only the matching
     * boolean value for this requirement.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array|bool
     */
    public function requirements($name = null)
    {
        $requirements = [
            'cacheFolderExists'     => !$this->getDefaultOption('cache_dir') || is_dir($this->getOption('cache_dir')),
            'cacheFolderIsWritable' => !$this->getDefaultOption('cache_dir') || is_writable($this->getOption('cache_dir')),
        ];

        if ($name) {
            if (!isset($requirements[$name])) {
                throw new InvalidArgumentException($name . ' is not in the requirements list (' . implode(', ', array_keys($requirements)) . ')', 19);
            }

            return $requirements[$name];
        }

        return $requirements;
    }

    /**
     * Render using the PHP engine.
     *
     * @param string $input    pug input or file
     * @param array  $vars     to pass to the view
     * @param string $filename optional file path
     *
     * @throws \Exception
     *
     * @return string
     */
    public function renderWithPhp($input, array $vars, $filename = null)
    {
        return parent::render($input, $vars, $filename);
    }

    /**
     * Render using the PHP engine.
     *
     * @param string $path pug input or file
     * @param array  $vars to pass to the view
     *
     * @throws \Exception
     *
     * @return string
     */
    public function renderFileWithPhp($path, array $vars)
    {
        return parent::renderFile($path, $vars);
    }

    /**
     * Compile HTML code from a Pug input or a Pug file.
     *
     * @param string $input    pug input or file
     * @param array  $vars     to pass to the view
     * @param string $filename optional file path
     *
     * @throws \Exception
     *
     * @return string
     */
    public function render($input, array $vars = [], $filename = null)
    {
        $fallback = function () use ($input, $vars, $filename) {
            return $this->renderWithPhp($input, $vars, $filename);
        };

        if ($this->getDefaultOption('pugjs')) {
            return $this->renderWithJs($input, null, $vars, $fallback);
        }

        return call_user_func($fallback);
    }

    /**
     * Compile HTML code from a Pug input or a Pug file.
     *
     * @param string $input pug file
     * @param array  $vars  to pass to the view
     *
     * @throws \Exception
     *
     * @return string
     */
    public function renderFile($path, array $vars = [])
    {
        $fallback = function () use ($path, $vars) {
            return $this->renderFileWithPhp($path, $vars);
        };

        if ($this->getDefaultOption('pugjs')) {
            return $this->renderFileWithJs($path, $vars, $fallback);
        }

        return call_user_func($fallback);
    }

    /**
     * Obsolete.
     *
     * @throws \ErrorException
     */
    public function stream()
    {
        throw new \ErrorException(
            '->stream() is no longer available, please use ' . StreamAdapter::class . ' instead',
            34
        );
    }
}
