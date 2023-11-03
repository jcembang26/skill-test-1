<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* themes/custom/demo/template/menu--main.html.twig */
class __TwigTemplate_c3c2c1bf17689b236b8f937847c7a967 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 23
        echo "<div class=\"wrapper\">
    <div class=\"logo\"><a href=\"/\">Logo</a></div>
";
        // line 25
        $macros["menus"] = $this->macros["menus"] = $this;
        // line 26
        echo "
";
        // line 31
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(twig_call_macro($macros["menus"], "macro_menu_links", [($context["items"] ?? null), ($context["attributes"] ?? null), 0], 31, $context, $this->getSourceContext()));
        echo "

";
        // line 52
        echo "</div>
        ";
    }

    // line 33
    public function macro_menu_links($__items__ = null, $__attributes__ = null, $__menu_level__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "items" => $__items__,
            "attributes" => $__attributes__,
            "menu_level" => $__menu_level__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start();
        try {
            // line 34
            $macros["menus"] = $this;
            // line 35
            if (($context["items"] ?? null)) {
                // line 36
                if ((($context["menu_level"] ?? null) == 0)) {
                    // line 37
                    echo "<ul";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["attributes"] ?? null), 37, $this->source), "html", null, true);
                    echo " class=\"nav-links\">
    ";
                } else {
                    // line 39
                    echo "    <ul class=\"drop-menu\">
        ";
                }
                // line 41
                echo "        ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                    // line 42
                    echo "        <li";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "attributes", [], "any", false, false, true, 42), 42, $this->source), "html", null, true);
                    echo " >
            ";
                    // line 43
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->getLink($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 43), 43, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 43), 43, $this->source)), "html", null, true);
                    echo "
            ";
                    // line 44
                    if (twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 44)) {
                        // line 45
                        echo "            ";
                        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(twig_call_macro($macros["menus"], "macro_menu_links", [twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 45), ($context["attributes"] ?? null), (($context["menu_level"] ?? null) + 1)], 45, $context, $this->getSourceContext()));
                        echo "
            ";
                    }
                    // line 47
                    echo "            </li>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 49
                echo "    </ul>
    ";
            }
            // line 51
            echo "    ";

            return ('' === $tmp = ob_get_contents()) ? '' : new Markup($tmp, $this->env->getCharset());
        } finally {
            ob_end_clean();
        }
    }

    public function getTemplateName()
    {
        return "themes/custom/demo/template/menu--main.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  122 => 51,  118 => 49,  111 => 47,  105 => 45,  103 => 44,  99 => 43,  94 => 42,  89 => 41,  85 => 39,  79 => 37,  77 => 36,  75 => 35,  73 => 34,  58 => 33,  53 => 52,  48 => 31,  45 => 26,  43 => 25,  39 => 23,);
    }

    public function getSourceContext()
    {
        return new Source("{#
/**
 * @file
 * Default theme implementation to display a menu.
 *
 * Available variables:
 * - menu_name: The machine name of the menu.
 * - items: A nested list of menu items. Each menu item contains:
 *   - attributes: HTML attributes for the menu item.
 *   - below: The menu item child items.
 *   - title: The menu link title.
 *   - url: The menu link url, instance of \\Drupal\\Core\\Url
 *   - localized_options: Menu link localized options.
 *   - is_expanded: TRUE if the link has visible children within the current
 *     menu tree.
 *   - is_collapsed: TRUE if the link has children within the current menu tree
 *     that are not currently visible.
 *   - in_active_trail: TRUE if the link is in the active trail.
 *
 * @ingroup themeable
 */
#}
<div class=\"wrapper\">
    <div class=\"logo\"><a href=\"/\">Logo</a></div>
{% import _self as menus %}

{#
We call a macro which calls itself to render the full tree.
@see https://twig.symfony.com/doc/1.x/tags/macro.html
#}
{{ menus.menu_links(items, attributes, 0) }}

{% macro menu_links(items, attributes, menu_level) %}
{% import _self as menus %}
{% if items %}
{% if menu_level == 0 %}
<ul{{ attributes }} class=\"nav-links\">
    {% else %}
    <ul class=\"drop-menu\">
        {% endif %}
        {% for item in items %}
        <li{{ item.attributes }} >
            {{ link(item.title, item.url) }}
            {% if item.below %}
            {{ menus.menu_links(item.below, attributes, menu_level + 1) }}
            {% endif %}
            </li>
            {% endfor %}
    </ul>
    {% endif %}
    {% endmacro %}
</div>
        ", "themes/custom/demo/template/menu--main.html.twig", "C:\\xampp8_1\\htdocs\\drupal-demo\\themes\\custom\\demo\\template\\menu--main.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("import" => 25, "macro" => 33, "if" => 35, "for" => 41);
        static $filters = array("escape" => 37);
        static $functions = array("link" => 43);

        try {
            $this->sandbox->checkSecurity(
                ['import', 'macro', 'if', 'for'],
                ['escape'],
                ['link']
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
