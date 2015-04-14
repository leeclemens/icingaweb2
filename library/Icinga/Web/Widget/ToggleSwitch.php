<?php
/* Icinga Web 2 | (c) 2013-2015 Icinga Development Team | GPLv2+ */

namespace Icinga\Web\Widget;

/**
 * ToggleSwitch widget
 *
 * This widget allows you to create a toggle switch for radio button like quick filters of the current view. Only one
 * switch with a particular set of parameters may be active at a time but not any of them just as well. Clicking on a
 * switch disables any other ones which share the same parameters.
 */
class ToggleSwitch extends AbstractWidget
{
    /**
     * The switches to be shown
     *
     * @var array
     */
    protected $switches = array();

    /**
     * Create and return a new ToggleSwitch
     *
     * @param   array   $switches   An array of arrays whose values are directly passed to ToggleSwitch::addSwitch()
     *
     * @return  ToggleSwitch
     */
    public static function create(array $switches)
    {
        $toggleSwitch = new static();
        foreach ($switches as $switch) {
            call_user_func_array(array($toggleSwitch, 'addSwitch'), $switch);
        }

        return $toggleSwitch;
    }

    /**
     * Add a new switch
     *
     * @param   array   $parameters     The url parameters to use
     * @param   string  $label          The label to use for the <a> tag
     * @param   array   $properties     The html properties for the <a> tag
     *
     * @return  $this
     */
    public function addSwitch(array $parameters, $label, array $properties = array())
    {
        $this->switches[] = array(
            'params'        => $parameters,
            'label'         => $label,
            'properties'    => $properties
        );

        return $this;
    }

    /**
     * Render and return this ToggleSwitch as HTML
     *
     * @return  string
     */
    public function render()
    {
        $html = '';
        foreach ($this->switches as $switch) {
            $active = true;
            foreach ($switch['params'] as $param => $value) {
                if ($this->view()->url->getParam($param) !== (string) $value) {
                    $active = false;
                    break;
                }
            }

            $properties = $switch['properties'];
            if (isset($properties['class'])) {
                $properties['class'] .= $active ? ' active' : ' inactive';
            } else {
                $properties['class'] = $active ? 'active' : 'inactive';
            }

            $url = $active
                ? $this->view()->url->without(array_keys($switch['params']))
                : $this->view()->url->with($switch['params']);
            $html .= $this->view()->qlink($switch['label'], $url, null, $properties);
        }

        return '<div class="toggle-switch">' . $html . '</div>';
    }
}
