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

/* modules/custom/drupaljira_timelog/templates/drupaljira-project-stats.html.twig */
class __TwigTemplate_ee6d7f3a905d725f4fafc1536f4fd402 extends Template
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
        // line 17
        yield "<div class=\"drupaljira-project-stats-card\">
  <h3>";
        // line 18
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Project Statistics: @title", ["@title" => ($context["project_title"] ?? null)]));
        yield "</h3>
  <ul class=\"drupaljira-stats-list\">
    <li><strong>";
        // line 20
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Total Tasks:"));
        yield "</strong> ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["total_tasks"] ?? null), "html", null, true);
        yield "</li>
    <li><strong>";
        // line 21
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Done Tasks:"));
        yield "</strong> ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["done_tasks"] ?? null), "html", null, true);
        yield "</li>
    <li><strong>";
        // line 22
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Total Estimate:"));
        yield "</strong> ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["total_estimate_formatted"] ?? null), "html", null, true);
        yield "</li>
    <li><strong>";
        // line 23
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Total Logged:"));
        yield "</strong> ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["total_logged_formatted"] ?? null), "html", null, true);
        yield "</li>
    <li>
      <strong>";
        // line 25
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Remaining Balance:"));
        yield "</strong>
      <span class=\"";
        // line 26
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar((((($tmp = ($context["is_overdue"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("stats-overdue") : ("stats-ok")));
        yield "\">
        ";
        // line 27
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["remaining_hours_formatted"] ?? null), "html", null, true);
        yield "
      </span>
    </li>
    <li><strong>";
        // line 30
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Overestimated Tasks:"));
        yield "</strong> ";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["overestimate_tasks"] ?? null), "html", null, true);
        yield "</li>
  </ul>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["project_title", "total_tasks", "done_tasks", "total_estimate_formatted", "total_logged_formatted", "is_overdue", "remaining_hours_formatted", "overestimate_tasks"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/custom/drupaljira_timelog/templates/drupaljira-project-stats.html.twig";
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
        return array (  91 => 30,  85 => 27,  81 => 26,  77 => 25,  70 => 23,  64 => 22,  58 => 21,  52 => 20,  47 => 18,  44 => 17,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/custom/drupaljira_timelog/templates/drupaljira-project-stats.html.twig", "/var/www/html/web/modules/custom/drupaljira_timelog/templates/drupaljira-project-stats.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = [];
        static $filters = ["t" => 18, "escape" => 20];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [],
                [0 => "t", 1 => "escape"],
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
