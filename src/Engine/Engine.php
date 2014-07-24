<?php

namespace Oneup\Twig\Engine;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class Engine implements EngineInterface
{
    protected $environment;
    protected $parser;

    public function __construct(\Twig_Environment $environment, TemplateNameParserInterface $parser)
    {
        $this->environment = $environment;
        $this->parser = $parser;
    }

    /**
     * Renders a template.
     *
     * @param string|TemplateReferenceInterface $name A template name or a TemplateReferenceInterface instance
     * @param array $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     *
     * @throws \RuntimeException if the template cannot be rendered
     *
     * @api
     */
    public function render($name, array $parameters = array())
    {
        return $this->load($name)->render($parameters);
    }

    /**
     * Returns true if the template exists.
     *
     * @param string|TemplateReferenceInterface $name A template name or a TemplateReferenceInterface instance
     *
     * @return bool    true if the template exists, false otherwise
     *
     * @throws \RuntimeException if the engine cannot handle the template name
     *
     * @api
     */
    public function exists($name)
    {
        // TODO: Implement exists() method.
    }

    /**
     * Returns true if this class is able to render the given template.
     *
     * @param string|TemplateReferenceInterface $name A template name or a TemplateReferenceInterface instance
     *
     * @return bool    true if this class supports the given template, false otherwise
     *
     * @api
     */
    public function supports($name)
    {
        if ($name instanceof \Twig_Template) {
            return true;
        }

        $template = $this->parser->parse($name);

        return 'twig' === $template->get('engine');
    }

    protected function load($name)
    {
        if ($name instanceof \Twig_Template) {
            return $name;
        }

        $template = $this->parser->parse($name);

        if (!file_exists($path = $template->getPath())) {
            throw new \InvalidArgumentException(sprintf('The template "%s" does not exist.', $name));
        }

        try {
            return $this->environment->loadTemplate(file_get_contents($template->getPath()));
        } catch (\Twig_Error_Loader $e) {
            throw new \InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }
}