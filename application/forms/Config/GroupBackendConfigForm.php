<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Forms\Config;

use Icinga\Forms\ConfigForm;
use Icinga\Web\Notification;
use Icinga\Application\Config;
use Icinga\Forms\Config\Group\LdapBackendForm;
use Icinga\Exception\NotImplementedError;


class GroupBackendConfigForm extends ConfigForm
{
    /**
     * The available resources split by type
     *
     * @var array
     */
    protected $resources;

    /**
     * Initialize this form
     */
    public function init()
    {
        $this->setName('form_config_groupbackend');
        $this->setSubmitLabel($this->translate('Save Changes'));
    }

    /**
     * Set the resource configuration to use
     *
     * @param   Config      $resourceConfig      The resource configuration
     *
     * @return  self
     */
    public function setResourceConfig(Config $resourceConfig)
    {
        $resources = array();
        foreach ($resourceConfig as $name => $resource) {
            $resources[strtolower($resource->type)][] = $name;
        }

        $this->resources = $resources;
        return $this;
    }

    /**
     * Return a form object for the given backend type
     *
     * @param   string      $type   The backend type for which to return a form
     *
     * @return  Form
     */
    public function getBackendForm($type)
    {
        if ($type === 'ini') {
            throw new NotImplementedError('INI group backends are not supported, yet');
            $form = new IniBackendForm();
        } elseif ($type === 'db') {
            throw new NotImplementedError('DB group backends are not supported, yet');
            $form = new DbBackendForm();
        } elseif ($type === 'ldap') {
            $form = new LdapBackendForm();
        } else {
            throw new InvalidArgumentException(sprintf($this->translate('Invalid backend type "%s" provided'), $type));
        }

        return $form;
    }

    /**
     * Add a particular group backend
     *
     * The backend to add is identified by the array-key `name'.
     *
     * @param   array   $values             The values to extend the configuration with
     *
     * @return  self
     *
     * @throws  InvalidArgumentException    In case the backend does already exist
     */
    public function add(array $values)
    {
        $name = isset($values['name']) ? $values['name'] : '';
        if (! $name) {
            throw new InvalidArgumentException($this->translate('Group backend name missing'));
        } elseif ($this->config->hasSection($name)) {
            throw new InvalidArgumentException($this->translate('Group backend already exists'));
        }

        unset($values['name']);
        $this->config->setSection($name, $values);
        return $this;
    }

    /**
     * Edit a particular group backend
     *
     * @param   string  $name               The name of the backend to edit
     * @param   array   $values             The values to edit the configuration with
     *
     * @return  array                       The edited backend configuration
     *
     * @throws  InvalidArgumentException    In case the backend does not exist
     */
    public function edit($name, array $values)
    {
        if (! $name) {
            throw new InvalidArgumentException($this->translate('Old group backend name missing'));
        } elseif (! ($newName = isset($values['name']) ? $values['name'] : '')) {
            throw new InvalidArgumentException($this->translate('New group backend name missing'));
        } elseif (! $this->config->hasSection($name)) {
            throw new InvalidArgumentException($this->translate('Unknown group backend provided'));
        }

        $backendConfig = $this->config->getSection($name);
        $this->config->removeSection($name);
        unset($values['name']);
        $this->config->setSection($newName, $backendConfig->merge($values));
        return $backendConfig;
    }

    /**
     * Remove the given group backend
     *
     * @param   string      $name           The name of the backend to remove
     *
     * @return  array                       The removed backend configuration
     *
     * @throws  InvalidArgumentException    In case the backend does not exist
     */
    public function remove($name)
    {
        if (! $name) {
            throw new InvalidArgumentException($this->translate('Group backend name missing'));
        } elseif (! $this->config->hasSection($name)) {
            throw new InvalidArgumentException($this->translate('Unknown group backend provided'));
        }

        $backendConfig = $this->config->getSection($name);
        $this->config->removeSection($name);
        return $backendConfig;
    }

    /**
     * Add or edit a group backend and save the configuration
     *
     * @see Form::onSuccess()
     */
    public function onSuccess()
    {
        $groupBackend = $this->request->getQuery('group_backend');
        try {
            if ($groupBackend === null) { // create new backend
                $this->add($this->getValues());
                $message = $this->translate('Group backend "%s" has been successfully created');
            } else { // edit existing backend
                $this->edit($groupBackend, $this->getValues());
                $message = $this->translate('Group backend "%s" has been successfully changed');
            }
        } catch (InvalidArgumentException $e) {
            Notification::error($e->getMessage());
            return;
        }

        if ($this->save()) {
            Notification::success(sprintf($message, $this->getElement('name')->getValue()));
        } else {
            return false;
        }
    }

    /**
     * Populate the form in case a group backend is being edited
     *
     * @see Form::onRequest()
     *
     * @throws  ConfigurationError      In case the backend name is missing in the request or is invalid
     */
    public function onRequest()
    {
        $groupBackend = $this->request->getQuery('group_backend');
        if ($groupBackend !== null) {
            if ($groupBackend === '') {
                throw new ConfigurationError($this->translate('Group backend name missing'));
            } elseif (! $this->config->hasSection($groupBackend)) {
                throw new ConfigurationError($this->translate('Unknown group backend provided'));
            } elseif ($this->config->getSection($groupBackend)->backend === null) {
                throw new ConfigurationError(
                    sprintf($this->translate('Backend "%s" has no `backend\' setting'), $groupBackend)
                );
            }

            $configValues = $this->config->getSection($groupBackend)->toArray();
            $configValues['type'] = $configValues['backend'];
            $configValues['name'] = $groupBackend;
            $this->populate($configValues);
        }
    }

    /**
     * @see Form::createElements()
     */
    public function createElements(array $formData)
    {
        $backendTypes = array();
        $backendTypes['ini'] = 'INI';
        if (isset($this->resources['db'])) {
            $backendTypes['db'] = $this->translate('Database');
        }
        if (isset($this->resources['ldap'])) {
            $backendTypes['ldap'] = 'LDAP';
        }

        $backendType = isset($formData['type']) ? $formData['type'] : null;
        if ($backendType === null) {
            $backendType = key($backendTypes);
        }

        $this->addElement(
            'select',
            'type',
            array(
                'ignore'            => true,
                'required'          => true,
                'autosubmit'        => true,
                'label'             => $this->translate('Backend Type'),
                'description'       => $this->translate(
                    'The type of resource to use for this group backend'
                ),
                'multiOptions'      => $backendTypes
            )
        );

        $this->addElements($this->getBackendForm($backendType)->createElements($formData)->getElements());
    }
}
