<?php

namespace Xend\WordPress\Users;

/**
 * User
 * @author dagan
 * @property int    $id
 * @property string $login
 * @property string $firstName
 * @property string $lastName
 * @property string $displayName
 * @property string $niceName
 * @property string $email
 * @property string $url
 * @property string $password
 * @property string $status
 * @property string $activationKey
 * @property array  $roles
 * @property array  $capabilities
 * @property array  $allCapabilities
 */
class User {
    
    /**
     * @param \WP_User
     */
    protected $_user;
    
    public function __construct(\WP_User $user) {
        $this->_user = $user;
    }
    
    public function __get($property) {
        $getterMethod = "_get" . ucfirst($property);
        $property     = $this->_convertPropertyName($property);
        $property2    = "user_" . $property;
        
        if (method_exists($this, $getterMethod)) {
            return $this->$getterMethod();
        } elseif (isset($this->_user, $property)) {
            return $this->_user->{$property};
        } elseif (isset($this->_user, $property2)) {
            return $this->_user->${property2};
        } else {
            return null;
        }
    }
    
    protected function _convertPropertyName($property) {
        return preg_replace_callback(
            '/([A-Z])/',
            function ($matches) { return "_" . strtolower($matches[1]); },
            $property);
    }
    
    protected function _getNiceName() {
        return $this->_user->user_nicename;
    }
    
    protected function _getPassword()
    {
        return $this->_user->user_pass;
    }
    
    protected function _getCapabilities() {
        return $this->_user->caps;
    }
    
    protected function _getAllCapabilities() {
        return $this->_user->all_caps;
    }
}