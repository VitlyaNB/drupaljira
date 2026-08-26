<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Sandbox\SecurityNotAllowedTestError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* themes/custom/drupaljira_theme/templates/paragraph--code.html.twig */
class __TwigTemplate_ff4dfd6ed39557ce54b4f5c3380c2494 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<div class=\"paragraph-code ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["language_class"] ?? null), "html", null, true);
        yield "\">
  ";
        // line 2
        if ((($tmp = ($context["clean_language"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 3
            yield "    <span class=\"code-language-badge\">";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["clean_language"] ?? null), "html", null, true);
            yield "</span>
  ";
        }
        // line 5
        yield "  <pre><code>";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["paragraph"] ?? null), "field_code", [], "any", false, false, true, 5), "value", [], "any", false, false, true, 5), "html", null, true);
        yield "</code></pre>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["language_class", "clean_language", "paragraph"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/drupaljira_theme/templates/paragraph--code.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  57 => 5,  51 => 3,  49 => 2,  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/drupaljira_theme/templates/paragraph--code.html.twig", "/var/www/html/web/themes/custom/drupaljira_theme/templates/paragraph--code.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 2];
        static $filters = ["escape" => 1];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "if"],
                [0 => "escape"],
                [],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            } elseif ($e instanceof SecurityNotAllowedTestError && isset($tests[$e->getTestName()])) {
                $e->setTemplateLine($tests[$e->getTestName()]);
            }

            throw $e;
        }

    }
}
