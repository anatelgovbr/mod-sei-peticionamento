<?php
/**
 * Generic whitelist persistence layer.
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetWhitelistBD extends InfraBD
{
    public function __construct(InfraIBanco $objInfraIBanco)
    {
        parent::__construct($objInfraIBanco);
    }
}
