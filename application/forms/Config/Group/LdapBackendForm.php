<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Forms\Config\Group;

use Icinga\Web\Form;

class LdapBackendForm extends Form
{
    /**
     * Initialize this form
     */
    public function init()
    {
        $this->setName('form_config_groupbackend_ldap');
    }

    /**
     * @see Form::createElements()
     */
    public function createElements(array $formData)
    {
        $this->addElement(
            'text',
            'group_base_dn',
            array(
                'required'  => true
            )
        );
        $this->addElement(
            'text',
            'group_attribute',
            array(
                'required'  => true
            )
        );
        $this->addElement(
            'text',
            'group_member_attribute',
            array(
                'required'  => true
            )
        );
        $this->addElement(
            'text',
            'group_class',
            array(
                'required'  => true
            )
        );

        return $this;
    }
}
